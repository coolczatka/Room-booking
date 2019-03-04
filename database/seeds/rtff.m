clear all;

%Przygotowanie danych

trainData = helperLoadData('train_FD001.txt');
validationData = helperLoadData('test_FD001.txt');
%Specify groups of variables of interest
varNames = string(trainData{1}.Properties.VariableNames);
timeVariable = varNames{2};
conditionVariables = varNames(3:5);
dataVariables = varNames(6:26);
%Wizualizacja próbki danych
nsample = 10;
figure
helperPlotEnsemble(trainData, timeVariable, ...
    [conditionVariables(1:2) dataVariables(1:2)], nsample)

%Working Regime Clustering

%Zamiana cell array na pojedyñcz¹ tabel
trainDataUnwrap = vertcat(trainData{:});
opConditionUnwrap = trainDataUnwrap(:, cellstr(conditionVariables));
%Wizualizacja punktow operacyjnych
figure
helperPlotClusters(opConditionUnwrap)
%uzycie k najbizszych sasiadów do klasyikacji ustawien
opts = statset('Display', 'final');
[clusterIndex, centers] = kmeans(table2array(opConditionUnwrap), 6, ...
    'Distance', 'sqeuclidean', 'Replicates', 5, 'Options', opts);
%Wizualizacja wyniku klasyfikacji
figure
helperPlotClusters(opConditionUnwrap, clusterIndex, centers)

%Normalizacja z uwzglednieniem klasy

%perform a normalization on measurements grouped by different working regimes
centerstats = struct('Mean', table(), 'SD', table());
for v = dataVariables
    centerstats.Mean.(char(v)) = splitapply(@mean, trainDataUnwrap.(char(v)), clusterIndex);
    centerstats.SD.(char(v))   = splitapply(@std,  trainDataUnwrap.(char(v)), clusterIndex);
end
centerstats.Mean
centerstats.SD
%Odnalezienie najblizszego srodka klasy dla kazdego pomiaru
trainDataNormalized = cellfun(@(data) regimeNormalization(data, centers, centerstats), ...
    trainData, 'UniformOutput', false);
%Wizualizacja
figure
helperPlotEnsemble(trainDataNormalized, timeVariable, dataVariables(1:4), nsample)

%Analiza trendu

%Wybranie czujnikow ktore w najwiekszym stopniu wplywaja na wynik
numSensors = length(dataVariables);
signalSlope = zeros(numSensors, 1);
warn = warning('off');
for ct = 1:numSensors
    tmp = cellfun(@(tbl) tbl(:, cellstr(dataVariables(ct))), trainDataNormalized, 'UniformOutput', false);
    mdl = linearDegradationModel(); % create model
    fit(mdl, tmp); % train mode
    signalSlope(ct) = mdl.Theta;
end
warning(warn);
%Sort the signal slopes and select 8 sensors with the largest slopes.
[~, idx] = sort(abs(signalSlope), 'descend');
sensorTrended = sort(idx(1:8))
%Visualize the selected trendable sensor measurements
figure
helperPlotEnsemble(trainDataNormalized, timeVariable, dataVariables(sensorTrended(3:6)), nsample)

%Construct Health Indicator

for j=1:numel(trainDataNormalized)
    data = trainDataNormalized{j};
    rul = max(data.time)-data.time;
    data.health_condition = rul / max(rul);
    trainDataNormalized{j} = data;
end
%Visualize the health condition
figure
helperPlotEnsemble(trainDataNormalized, timeVariable, "health_condition", nsample)
% fit a linear regression model of Health Condition
trainDataNormalizedUnwrap = vertcat(trainDataNormalized{:});

sensorToFuse = dataVariables(sensorTrended);
X = trainDataNormalizedUnwrap{:, cellstr(sensorToFuse)};
y = trainDataNormalizedUnwrap.health_condition;
regModel = fitlm(X,y);
bias = regModel.Coefficients.Estimate(1)
weights = regModel.Coefficients.Estimate(2:end)
%Construct a single health indicator by multiplying the sensor measurements with their associated weights
trainDataFused = cellfun(@(data) degradationSensorFusion(data, sensorToFuse, weights), trainDataNormalized, ...
    'UniformOutput', false);
%Visualize the fused health indicator for training data
figure
helperPlotEnsemble(trainDataFused, [], 1, nsample)
xlabel('Time')
ylabel('Health Indicator')
title('Training Data')

%Apply same operation to validation data
validationDataNormalized = cellfun(@(data) regimeNormalization(data, centers, centerstats), ...
    validationData, 'UniformOutput', false);
validationDataFused = cellfun(@(data) degradationSensorFusion(data, sensorToFuse, weights), ...
    validationDataNormalized, 'UniformOutput', false);
%visualize
figure
helperPlotEnsemble(validationDataFused, [], 1, nsample)
xlabel('Time')
ylabel('Health Indicator')
title('Validation Data')

%Build Similarity RUL Model

mdl = residualSimilarityModel(...
    'Method', 'exp1',...
    'Distance', 'absolute',...
    'NumNearestNeighbors', 50,...
    'Standardize', 1);

fit(mdl, trainDataFused);

%Performence Evaluation

%To evaluate the similarity RUL model, use 50%, 70% and 90% of a sample validation data to predict its RUL
breakpoint = [0.5, 0.7, 0.9];
validationDataTmp = validationDataFused{3}; % use one validation data for illustration
%Use the validation data before the first breakpoint, which is 50% of the lifetime
bpidx = 1;
validationDataTmp50 = validationDataTmp(1:ceil(end*breakpoint(bpidx)),:);
trueRUL = length(validationDataTmp) - length(validationDataTmp50);
[estRUL, ciRUL, pdfRUL] = predictRUL(mdl, validationDataTmp50);
%Visualize the validation data truncated at 50% and its nearest neighbors.
figure
compare(mdl, validationDataTmp50);
%Visualize the estimated RUL compared to the true RUL and the probability distribution of the estimated RUL
figure
helperPlotRULDistribution(trueRUL, estRUL, pdfRUL, ciRUL)
%Use the validation data before the second breakpoint, which is 70% of the lifetime
bpidx = 2;
validationDataTmp70 = validationDataTmp(1:ceil(end*breakpoint(bpidx)), :);
trueRUL = length(validationDataTmp) - length(validationDataTmp70);
[estRUL,ciRUL,pdfRUL] = predictRUL(mdl, validationDataTmp70);
%Propability destiny
figure
compare(mdl, validationDataTmp70);
%90% of lifetime
bpidx = 3;
validationDataTmp90 = validationDataTmp(1:ceil(end*breakpoint(bpidx)), :);
trueRUL = length(validationDataTmp) - length(validationDataTmp90);
[estRUL,ciRUL,pdfRUL] = predictRUL(mdl, validationDataTmp90);

figure
compare(mdl, validationDataTmp90);
figure
helperPlotRULDistribution(trueRUL, estRUL, pdfRUL, ciRUL)
%Now repeat the same evaluation procedure for the whole validation data set 
%and compute the error between estimated RUL and true RUL for each breakpoint.
numValidation = length(validationDataFused);
numBreakpoint = length(breakpoint);
error = zeros(numValidation, numBreakpoint);
%visualize histogram
[pdf50, x50] = ksdensity(error(:, 1));
[pdf70, x70] = ksdensity(error(:, 2));
[pdf90, x90] = ksdensity(error(:, 3));

figure
ax(1) = subplot(3,1,1);
hold on
histogram(error(:, 1), 'BinWidth', 5, 'Normalization', 'pdf')
plot(x50, pdf50)
hold off
xlabel('Prediction Error')
title('RUL Prediction Error using first 50% of each validation ensemble member')

ax(2) = subplot(3,1,2);
hold on
histogram(error(:, 2), 'BinWidth', 5, 'Normalization', 'pdf')
plot(x70, pdf70)
hold off
xlabel('Prediction Error')
title('RUL Prediction Error using first 70% of each validation ensemble member')

ax(3) = subplot(3,1,3);
hold on
histogram(error(:, 3), 'BinWidth', 5, 'Normalization', 'pdf')
plot(x90, pdf90)
hold off
xlabel('Prediction Error')
title('RUL Prediction Error using first 90% of each validation ensemble member')
linkaxes(ax)
for dataIdx = 1:numValidation
    tmpData = validationDataFused{dataIdx};
    for bpidx = 1:numBreakpoint
        tmpDataTest = tmpData(1:ceil(end*breakpoint(bpidx)), :);
        trueRUL = length(tmpData) - length(tmpDataTest);
        [estRUL, ~, ~] = predictRUL(mdl, tmpDataTest);
        error(dataIdx, bpidx) = estRUL - trueRUL;
    end
end
figure
boxplot(error, 'Labels', {'50%', '70%', '90%'})
ylabel('Prediction Error')
title('Prediction error using different percentages of each validation ensemble member')
%Plot the prediction error in box plot to visualize the median, 25-75 quantile and outliers.

%    for ii=3:26
%        train_FD002(:,ii) = (train_FD002(:,ii)-min(train_FD002(:,ii)))./(max(train_FD002(:,ii))-min(train_FD002(:,ii))).*2-1;
%    end
%    names = ["id" "time" "operational_setting_1" "operational_setting_2" "operational_setting_3"];
%    name = "sensor_measurement_";
%    for ii=6:26
%        names(ii) = name+string(ii-5);
%    end
%    trainData = array2table(train_FD002,"VariableNames",names);
%    conditionVariables = names(3:5);
%    dataVariables = names(6:26);
%     
%    opConditionUnwrap = train_FD002(:,3:5);
%    [clusterIndex, centers] = kmeans(opConditionUnwrap, 6, 'Replicates', 6);
%    figure
%    plot3(opConditionUnwrap(:,1),opConditionUnwrap(:,2),opConditionUnwrap(:,3),"O",centers(:,1),centers(:,2),centers(:,3),"X")
%    %Analiza trendów
%    numSensors = length(dataVariables);
%    signalSlope = zeros(numSensors,1);
%    for ct=1:numSensors
%        tmp = trainData(:,ct+5);
%        mdl = linearDegradationModel();
%        fit(mdl,tmp);       signalSlope(ct)=mdl.Theta;
%    end
%    [~,idx] = sort(abs(signalSlope),'descend');
%    sensorTrended = sort(idx(1:8));
%    [h,w]=size(trainData);
%   
%    for ii=1:h
%        data = trainData(ii,:);
%        did = trainData(trainData.id==data.id,:);
%        rul = max(did.time)-data.time;
%        [he,wi] = size(did);
%        data.health_condition = rul / he;%he = max_rul
%        new{ii} = data;
%    end
%   clear Y;
%   sensorToFuse = dataVariables(sensorTrended);
%   X = trainData{:,cellstr(sensorToFuse)};
%   for ii=1:length(new)
%       Y(ii,1)=new{ii}.health_condition;
%   end
%  regModel = fitlm(X,Y);
%  bias = regModel.Coefficients.Estimate(1)
%  weights = regModel.Coefficients.Estimate(2:end)
% trainDataFused = degradationSensorFusion(trainData, sensorToFuse, weights);
% figure
% plot(1:149,trainDataFused(1:149),'r',1:length(150:418),trainDataFused(150:418),'b')
%  mdl = residualSimilarityModel(...
%      'Method', 'exp1',...
%      'Distance', 'absolute',...
%      'NumNearestNeighbors', 50,...
%      'Standardize', 1);
%  c = 1;
%  sizes = zeros(260,1);
%  for ii=1:260
%      did = trainData(trainData.id==c,:);
%      [he,wi] = size(did);
%      sizes(c)=he;
%      c=c+1;
%  end
%  c=1;
%  trainDataFusedb = cell(260,1);
%  for ii=1:260
%    trainDataFusedb{ii} = trainDataFused(c:c+sizes(ii)-1);
%    c = c+sizes(ii);
%  end
%  fit(mdl,trainDataFusedb);
%  [estRUL, ciRUL, pdfRUL] = predictRUL(mdl,degradationSensorFusion(trainData(1:122,:),sensorToFuse, weights));
%  compare(mdl,degradationSensorFusion(trainData(1:122,:),sensorToFuse, weights));