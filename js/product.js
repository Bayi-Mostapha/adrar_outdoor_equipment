const colorWrappers = document.querySelectorAll(".product-color-wrapper");
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
plusBtn.addEventListener("click", (e) => {
    e.preventDefault();
    let quantity = parseInt(quantityInput.value);
    quantityInput.value = quantity + 1;
});
minusBtn.addEventListener("click", (e) => {
    e.preventDefault();
    let quantity = parseInt(quantityInput.value);
    if (quantity >= 2)
        quantityInput.value = quantity - 1;
});