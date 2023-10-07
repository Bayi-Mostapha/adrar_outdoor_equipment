const fileInput = document.getElementById("image");
const preview = document.getElementById("preview");
if (fileInput && preview)
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

let colorInputContainers = document.querySelectorAll(".input-color");
const addColor = document.querySelector(".add-color-input");
if (addColor)
    addColor.addEventListener("click", () => {
        const container = document.querySelector(".color-inputs");

        let colorContainer = document.createElement("div");
        colorContainer.className = "input-color";

        let newInput = document.createElement("input");
        newInput.type = "color";
        newInput.name = "color[]";

        let closeBtn = document.createElement("button");
        closeBtn.className = "remove-color-input";
        closeBtn.type = "button";
        closeBtn.innerHTML = "<i class=\"fa-solid fa-xmark\"></i>";

        colorContainer.appendChild(newInput);
        colorContainer.appendChild(closeBtn);
        container.appendChild(colorContainer);

        colorInputContainers = document.querySelectorAll(".input-color");
        removeInput();
    });
function removeInput() {
    colorInputContainers.forEach(colorInputContainer => {
        removeColor = colorInputContainer.querySelector(".remove-color-input");
        removeColor.addEventListener("click", () => {
            colorInputContainer.querySelector("input").name = "not-color";
            colorInputContainer.classList.add("remove");
        });
    });
}
if (colorInputContainers)
    removeInput();