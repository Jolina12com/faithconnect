import { HMSClient } from 'https://cdn.skypack.dev/@100mslive/hms-video-store?min';

hmsManager = new HMS.WebrtcHMS();

window.HMSClient = hmsManager;
console.log('HMSClient initialized:', hmsManager);
