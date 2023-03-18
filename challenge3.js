
if (typeof (SERVER_DOMAIN) === 'undefined') {
    window.location.replace("/unconfigured.html");
}

const RECEIVE_URL = SERVER_DOMAIN + "/s_child.html" + "?origin=" + get_domain();

var window_ref = null;

function send_message(destination) {
    const message = document.getElementById("message").value;
    // Validate the user input and check the destination
    if (isValidDomain(destination) && isValidInput(message)) {
        receiver.contentWindow.postMessage(message, SERVER_DOMAIN, {
            origin: get_domain(),
            allowSameOrigin: false,
            allowScriptAccess: false,
            allowFormData: false
        });
    }
}

function get_domain() {
    return window.location.origin;
}

function isValidInput(message) {
    // Validate the user input
    return /^[a-zA-Z0-9_\s]+$/.test(message);
}

function isValidOrigin(origin) {
    // Validate the origin of the message
    return origin === get_domain();
}

function isValidDomain(domain) {
    const allowedDomains = [SERVER_DOMAIN];
    return allowedDomains.includes(domain);
}

window.addEventListener("message", function (event) {
    if (isValidOrigin(event.origin)) {
        // Process the event.data here, e.g., update UI
    }
}, false);

var receiver = document.getElementById("s_iframe");
receiver.src = RECEIVE_URL;

const sendMessageButton = document.getElementById("send_message_button");
sendMessageButton.addEventListener("click", function () {
    send_message(SERVER_DOMAIN);
}, false);
