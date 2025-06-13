let selectmenu = document.getElementById("amenities"); 
selectmenu.addEventListener("change", function() {
    let selectedOption = selectmenu.options[selectmenu.selectedIndex].value;
    
let httpRequest = new XMLHttpRequest();
httpRequest.open("POST", "/updateStatus", true);
httpRequest.setRequestHeader("Content-Type", "application/json");
httpRequest.send(JSON.stringify({ status: selectedOption }));
httpRequest.onreadystatechange = function() {
    if (httpRequest.readyState === XMLHttpRequest.DONE) {
        if (httpRequest.status === 200) {
            console.log("Status updated successfully");
        } else {
            console.error("Error updating status");
        }
    }
};
}   );
