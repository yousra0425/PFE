import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

const echo = new Echo({
    broadcaster: "pusher",
    key: "475632ef710fd2bf9ddf",
    cluster: "mt1",
    forceTLS: true,
    encrypted: true,
    authEndpoint: 'http://127.0.0.1:8000/broadcasting/auth',
    auth: {
        headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`, // or from state
        },
    },
});

export default echo;
