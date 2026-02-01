import { slideTemplate } from "./templates/slideTemplate.js";
import { enviarEmail } from "./js/mail.js";
window.onload = function() {
    slideTemplate();
    enviarEmail()
}