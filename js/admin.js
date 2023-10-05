const dateElement = document.querySelector(".date");
const currentDate = new Date();
const year = currentDate.getFullYear();
const month = currentDate.getMonth();
const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
];
const day = currentDate.getDate();
const dateString = `${day} ${months[month]} ${year}`;
dateElement.textContent = `${dateString}`;

// ***********************

const timeElement = document.querySelector(".time");
let seconds = currentDate.getSeconds();
let hours = currentDate.getHours();
let minutes = currentDate.getMinutes();
let timeString = `${hours}:${minutes}`;
timeElement.textContent = `${timeString}`;

function updateClock() {
    seconds++;
    if (seconds >= 60) {
        minutes++;
        seconds = 0;
    }
    if (minutes >= 60) {
        hours++;
        minutes = 0;
    }
    if (hours >= 24) {
        hours = 0;
        seconds = 0;
        minutes = 0;

        const day = currentDate.getDate();
        const dateString = `${day} ${months[month]} ${year}`;
        dateElement.textContent = `${dateString}`;
    }
    const hoursString = (hours < 10) ? '0' + hours.toString() : hours.toString();
    const minutesString = (minutes < 10) ? '0' + minutes.toString() : minutes.toString();
    timeString = `${hoursString}:${minutesString}`;
    timeElement.textContent = timeString;
}
setInterval(updateClock, 1000);