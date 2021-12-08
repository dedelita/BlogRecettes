function randomColor() {
    var colors = ["#FF851B", "#B10DC9", "#0074D9", "#85144b", "#3D9970"]
    var color = "#";
    var randomHex = "123456ABCDEF";  
    for(var i = 0; i<6; i++){
        color+= randomHex[Math.floor(Math.random()*16)]
    }
    return colors[Math.floor(Math.random() * colors.length)];
}
document.addEventListener("DOMContentLoaded", function() {
    const nbComments = document.querySelector('.comments').dataset.nbComments;
    for(var i = 0; i < nbComments; i++) {
        var newColor = randomColor();
        document.getElementById("writer_" + i).style.borderColor = newColor;
        document.getElementById("writer_" + i).style.color = newColor;
    }
})