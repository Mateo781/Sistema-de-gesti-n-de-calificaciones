function filtrarTabla() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toLowerCase();
    let tbody = document.getElementById("tableBody");
    let tr = tbody.getElementsByTagName("tr");

    for (let i = 0; i < tr.length; i++) {
        let tdAlumno = tr[i].getElementsByTagName("td")[0];
        let tdMateria = tr[i].getElementsByTagName("td")[1];
        let tdProfesor = tr[i].getElementsByTagName("td")[5];

        if (tdAlumno || tdMateria || tdProfesor) {
            let txtAlumno = tdAlumno ? tdAlumno.textContent || tdAlumno.innerText : "";
            let txtMateria = tdMateria ? tdMateria.textContent || tdMateria.innerText : "";
            let txtProfesor = tdProfesor ? tdProfesor.textContent || tdProfesor.innerText : "";

            if (txtAlumno.toLowerCase().indexOf(filter) > -1 || 
                txtMateria.toLowerCase().indexOf(filter) > -1 || 
                txtProfesor.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}