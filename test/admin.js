document.addEventListener("DOMContentLoaded", function () {
    const roomId = "user_room_123"; 
    let adminPeer = new Peer(); 

    adminPeer.on("open", (id) => {
        console.log("Admin Connected with ID:", id);

        
        fetch(`/test/get-user.php?roomId=${roomId}`)
            .then((response) => response.json())
            .then((userData) => {
                if (userData && userData.user_peer_id) {
                    console.log("User peer ID:", userData.user_peer_id);

                  
                    let call = adminPeer.call(userData.user_peer_id, null); 

                    call.on("stream", (userStream) => {
                        console.log("User's video stream received!");

                        let videoElement = document.createElement("video");
                        videoElement.srcObject = userStream;
                        videoElement.autoplay = true;
                        videoElement.setAttribute("playsinline", "true");

                        document.body.appendChild(videoElement);

                        videoElement.play().catch((error) => {
                            console.log("Autoplay blocked, creating unmute button...");
                            
                            let unmuteButton = document.createElement('button');
                            unmuteButton.textContent = 'Click to unmute and play';
                            unmuteButton.onclick = () => {
                                videoElement.muted = false;
                                videoElement.play();
                                unmuteButton.remove();
                            };
                            document.body.appendChild(unmuteButton);
                        });
                    });
                } else {
                    console.log("No user found for this room.");
                }
            })
            .catch((err) => {
                console.error("Error fetching user data:", err);
            });
    });

    adminPeer.on("error", (err) => {
        console.error("PeerJS error:", err);
    });
});
