const searchInput = document.getElementById("searchInput");
const tableBody = document.getElementById("tableBody");

searchInput.addEventListener("keyup", () => {

  const filter = searchInput.value.toLowerCase();

  const rows = tableBody.querySelectorAll("tr");

  rows.forEach(row => {

    const text = row.innerText.toLowerCase();

    if(text.includes(filter)){
      row.style.display = "";
    }else{
      row.style.display = "none";
    }

  });

});