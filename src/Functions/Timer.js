 

// function startAlarmTimer() {
//   clearTimeout(timeoutId);
//   timeoutId = setTimeout(() => {
//     Alarm();
//   }, 10000);
//   console.log("Alarm timer started");
// }
// function Alarm() {
//   if (doorStatus === 1) {
//     doorStatus = 2;
//   }
//   else{
//     return;
//   }
// }

// function TimerStop() {
//   clearTimeout(timeoutId);  
//   console.log("Alarm timer stopped");
// }

// function setDoorStatus(newStatus) {
//   doorStatus = newStatus;

//   if (doorStatus === 1) {
//     startAlarmTimer();
//   } if (doorStatus === 0){ 
//     TimerStop();
//   }
// }
