<template>
  <div class="join-container">
    <h2>Scan QR Code</h2>

    <qrcode-stream 
      @decode="onDecode" 
      @init="onInit" 
      @detect="onDetect"
    />

    <div class="status">
      <p v-if="qrDetected"><strong>QR Code Detected!</strong></p>
      <p v-else>Point the camera at a QR code.</p>
    </div>

    <div class="scanned" v-if="scannedEventId">
      <p><strong>Scanned Event ID:</strong> {{ scannedEventId }}</p>
    </div>

    <div v-if="loading" class="loading">Processing…</div>

    <div v-if="error" class="error">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { QrcodeStream, setZXingModuleOverrides } from "vue-qrcode-reader";
import * as ZXingBrowser from "@zxing/browser"; // <-- correct import
import api from "@/services/api";

setZXingModuleOverrides({
  BrowserQRCodeReader: ZXingBrowser.BrowserQRCodeReader
});


const scannedEventId = ref(null);
const loading = ref(false);
const error = ref(null);
const qrDetected = ref(false);

const onDecode = async (result) => {
  const eventId = result.trim();
  scannedEventId.value = eventId;
  error.value = null;
  qrDetected.value = false; // reset detection

  loading.value = true;
  console.log("QR decoded:", result);

  try {
    const res = await api.post("/participations/scan", {
      event_id: eventId,
    });
    

    if (res.data.status === "already_in") {
      const confirmOut = confirm(
        "You're already participating in this event. Time-out now?"
      );

      if (confirmOut) {
        await api.post("/participations/out", { event_id: eventId });
        alert("You have timed out.");
      } else {
        alert("You stayed checked-in.");
      }
    } else {
      alert("Participation recorded!");
    }
  } catch (err) {
    error.value =
      err.response?.data?.message || "Something went wrong while scanning.";
  } finally {
    loading.value = false;
  }
};

const onDetect = (result) => {
  // Fires continuously while a QR code is in view
  qrDetected.value = !!result; // true if QR code is detected
};

const onInit = (promise) => {
  promise.catch((err) => {
    if (err.name === "NotAllowedError") {
      error.value = "Camera access denied.";
    } else if (err.name === "NotFoundError") {
      error.value = "No camera found.";
    } else {
      error.value = "Camera initialization failed.";
    }
  });
};
</script>

<style scoped>
.join-container {
  max-width: 500px;
  margin: 20px auto;
  text-align: center;
}

.status {
  margin-top: 15px;
  padding: 10px;
  background: #eef;
  border-radius: 6px;
}

.scanned {
  margin-top: 15px;
  padding: 10px;
  background: #def;
  border-radius: 6px;
}

.loading {
  margin-top: 15px;
  color: #333;
}

.error {
  margin-top: 15px;
  color: red;
}
</style>
