const localVideo = document.getElementById('localVideo');
const remoteVideo = document.getElementById('remoteVideo');
const startCallButton = document.getElementById('startCall');

let localStream;
let remoteStream = new MediaStream();
let peerConnection;

const servers = {
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' }, // STUN server
        {
            urls: 'turn:your-turn-server.com', // Replace with your TURN server details
            username: 'user',
            credential: 'password'
        }
    ]
};

// Get local media (video/audio)
async function getLocalStream() {
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        localVideo.srcObject = localStream;
    } catch (error) {
        console.error('Error accessing media devices.', error);
    }
}

// Initialize PeerConnection
function createPeerConnection() {
    peerConnection = new RTCPeerConnection(servers);

    // Add local stream tracks to the connection
    localStream.getTracks().forEach((track) => {
        peerConnection.addTrack(track, localStream);
    });

    // Listen for remote stream tracks
    peerConnection.ontrack = (event) => {
        event.streams[0].getTracks().forEach((track) => {
            remoteStream.addTrack(track);
        });
        remoteVideo.srcObject = remoteStream;
    };

    // Handle ICE candidates
    peerConnection.onicecandidate = (event) => {
        if (event.candidate) {
            console.log('New ICE candidate:', event.candidate);
            sendSignalingMessage({ type: 'candidate', candidate: event.candidate });
        }
    };

    // Log connection state
    peerConnection.onconnectionstatechange = () => {
        console.log('Connection state:', peerConnection.connectionState);
    };
}

// Create offer
async function makeOffer() {
    const offer = await peerConnection.createOffer();
    await peerConnection.setLocalDescription(offer);
    sendSignalingMessage({ type: 'offer', offer });
}

// Create answer
async function handleOffer(offer) {
    await peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
    const answer = await peerConnection.createAnswer();
    await peerConnection.setLocalDescription(answer);
    sendSignalingMessage({ type: 'answer', answer });
}

// Handle answer
async function handleAnswer(answer) {
    await peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
}

// Handle ICE candidate
function handleCandidate(candidate) {
    peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
}

// Simulated signaling server (replace this with your signaling logic)
function sendSignalingMessage(message) {
    // Simulate message exchange
    setTimeout(() => {
        if (message.type === 'offer') {
            handleOffer(message.offer);
        } else if (message.type === 'answer') {
            handleAnswer(message.answer);
        } else if (message.type === 'candidate') {
            handleCandidate(message.candidate);
        }
    }, 500);
}

// Start Call
startCallButton.addEventListener('click', async () => {
    await getLocalStream();
    createPeerConnection();
    makeOffer();
});
