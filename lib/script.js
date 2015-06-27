//prepinam obrazek
setTimeout("changepic_up()", ran());
function changepic_up() {
    document.images['logo'].src = './img/logo_rozsvicene.gif'
    setTimeout("changepic_down()", ran());
}

function changepic_down() {
    document.images['logo'].src = './img/logo_zhasnute.gif'
    setTimeout("changepic_up()", ran());
}

function ran()
{
    var randomnumber = Math.floor(Math.random() * 2);
    randomnumber = (randomnumber * 1000) + 1000;
    return randomnumber;
}

function close_fancybox(t) {
    setTimeout("parent.$.fancybox.close()", t);
}

function close_fancybox_redirect_parent(link, t)
{
    setTimeout(redirect_parent(link), t);

}

function redirect_parent(link) {
    window.parent.location.href = link;
}

function  add_more_foto() {
  var txt = "<br /><input type=\"file\" class=\"big\" name=\"fileToUpload[]\">";
  document.getElementById("addfoto").innerHTML += txt;
}


function rolovat_recenzi_down_gurmani(id, id2, id3) {
    var element = document.getElementById(id);
    element.className = element.className.replace("card_prehledy70", "card_prehledy70_bezvysky");

    var element2 = document.getElementById(id2);
    element2.className += " hidden";

    var element3 = document.getElementById(id3);
    element3.className = element3.className.replace("hidden", "");
}

function rolovat_recenzi_up_gurmani(id, id2, id3) {
    var element = document.getElementById(id);
    element.className = element.className.replace("card_prehledy70_bezvysky", "card_prehledy70");

    var element2 = document.getElementById(id2);
    element2.className = element2.className.replace("hidden", "");

    var element3 = document.getElementById(id3);
    element3.className += " hidden";
}

function rolovat_recenzi_down_aktuality(id, id2, id3) {
    var element = document.getElementById(id);
    element.className = element.className.replace("card_prehledy70", "card_prehledy70_bezvysky");

    var element2 = document.getElementById(id2);
    element2.className += " hidden";

    var element3 = document.getElementById(id3);
    element3.className = element3.className.replace("hidden", "");
}

function rolovat_recenzi_up_aktuality(id, id2, id3) {
    var element = document.getElementById(id);
    element.className = element.className.replace("card_prehledy70_bezvysky", "card_prehledy70");

    var element2 = document.getElementById(id2);
    element2.className = element2.className.replace("hidden", "");

    var element3 = document.getElementById(id3);
    element3.className += " hidden";
}

function show_hide(id1, id2) {
    var element = document.getElementById(id1);
    element.className = element.className.replace("hidden", "");

    var element2 = document.getElementById(id2);
    element2.className += " hidden";    
}