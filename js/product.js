const colorWrappers = document.querySelectorAll(".product-color-wrapper");
if (colorWrappers)
    colorWrappers.forEach(colorWrapper => {
        const radio = colorWrapper.querySelector(".color-radio");
        const label = colorWrapper.querySelector("label");

        radio.addEventListener("click", () => {
            colorWrappers.forEach(otherColorWrapper => {
                otherColorWrapper.querySelector("label").classList.remove("checked");
            });
            label.classList.add("checked");
        });
    });

const quantityInput = document.querySelector(".quantity");
const plusBtn = document.querySelector(".plus");
const minusBtn = document.querySelector(".minus");
if (plusBtn && quantityInput)
    plusBtn.addEventListener("click", (e) => {
        e.preventDefault();
        let quantity = parseInt(quantityInput.value);
        quantityInput.value = quantity + 1;
    });
if (minusBtn && quantityInput)
    minusBtn.addEventListener("click", (e) => {
        e.preventDefault();
        let quantity = parseInt(quantityInput.value);
        if (quantity >= 2)
            quantityInput.value = quantity - 1;
    });
if (quantityInput)
    quantityInput.addEventListener('input', () => {
        let n = quantityInput.value.replace(/[^0-9]/g, '');
        if (n == '')
            n = 1;
        quantityInput.value = n;
    });