document.addEventListener("DOMContentLoaded",(event) =>{
    setTimeout(() => {
        document.querySelector("#load-iframe-map").innerHTML = `
        <iframe class="contact__iframe" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"  style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d24309.63534698699!2d-3.7767084137867593!3d40.393243207479294!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd418817aae17a17%3A0x697622d481b98f09!2sAluche%2C%20Latina%2C%20Madrid!5e0!3m2!1ses!2ses!4v1707481871555!5m2!1ses!2ses"></iframe>
        `;
    },500);
});