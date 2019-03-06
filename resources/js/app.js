function toggleDa() {
    let chckd = $('#active_only').prop("checked");

    $('#active_only').change(() => {
        dbld = document.getElementsByClassName('sbmt');
        if (!chckd) {
            for (let i = 0; i < dbld.length; i++) {
                dbld[i].parentElement.parentElement.parentElement.parentElement.style.display = "none";
            }
            chckd = true;
        } else {
            for (let i = 0; i < dbld.length; i++) {
                dbld[i].parentElement.parentElement.parentElement.parentElement.removeAttribute("style");
            }
            chckd = false;
        }
    })
}