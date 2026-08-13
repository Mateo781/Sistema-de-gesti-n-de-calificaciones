const alumnoSelect = document.getElementById("alumnoSelect");
const btnConsultarAlumno = document.getElementById("btnConsultarAlumno");

if (alumnoSelect && btnConsultarAlumno) {

    btnConsultarAlumno.addEventListener("click", () => {

        const alumnoId = alumnoSelect.value;

        if (!alumnoId) {
            return;
        }

        window.location.href =
            "index.php?p=tutor_sit_academica&alumno_id=" + alumnoId;
    });

}