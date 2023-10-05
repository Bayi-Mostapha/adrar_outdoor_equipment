const fileInput = document.getElementById("image");
const preview = document.getElementById("preview");

fileInput.addEventListener("change", function () {
    const selectedFile = fileInput.files[0];

    if (selectedFile && selectedFile.type.startsWith("image/")) {
        const reader = new FileReader();

        reader.onload = function (e) {
            const img = document.createElement("img");
            img.src = e.target.result;
            preview.classList.add("visible");
            preview.innerHTML = "";
            preview.appendChild(img);
        };

        reader.readAsDataURL(selectedFile);
    } else {
        preview.innerHTML = "Please select a valid image file.";
    }
});

const addColor = document.querySelector(".add-color-input");
if (addColor)
    addColor.addEventListener("click", () => {
        const container = document.querySelector(".color-inputs");
        let newInput = document.createElement("input");
        newInput.type = "color";
        newInput.name = "color[]";

        container.appendChild(newInput);
    });
