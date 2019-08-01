'use strict'
document.addEventListener('DOMContentLoaded', e => {
    let date = new Date()
    const year = date.getFullYear()
    let yearContent = document.querySelector('#year')
    yearContent.innerHTML = year

// initiate a fetch call
fetch('scripts/display.php')
    .then(response => {
        return response.json()
    })
    .then(data => {
        console.log(data)
        for (var i = 0; i < data.length; i++) {

            $("#data").append("<tr><td>" + data[i].id + "</td><td>" + data[i].firstName + "</td><td>" + data[i].middleName + "</td><td>" + data[i].lastName + "</td><td>" + data[i].email + "</td><td>" + data[i].phone + "</td><td>" + data[i].created_at + "</td></tr>");
        };
    })
    .catch(error => {
        console.log('The Request Failed', error)
    })
})
