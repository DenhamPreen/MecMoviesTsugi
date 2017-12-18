var index = 0; // consider using chapter urls length
var chapter = 0;

console.log(chapters[chapter].name);
function onloadHandler(){
	buildMenu();
	slidesHtml(0,0);
}

function selectChapter(chapterIndex){
	chapterIndex--;
	var outOfRange = (chapterIndex > chapters.length) || (chapterIndex < 0);
	if (outOfRange){
		toastr.error("Not a valid chapter")
	} else {
		chapter = chapterIndex;
		index = 0;
		slidesHtml(chapterIndex, index);
	}
	console.log(chapters[chapter].name);
}

function changeChapter(forward){

	var outOfRange = (chapter >= (chapters.length - 1) && forward) || ((chapter - 1 < 0 ) && !forward);
	if (outOfRange){
		toastr.error("End of Chapters")
	} else {
		//document.getElementById("chapter"+chapter).style.backgroundColor = "pink";
//		document.getElementById("chapter"+chapter).className += " lighten";
		console.log(forward);
		forward ? chapter++ : chapter--;
		console.log(chapter);
		console.log(chapters[chapter].name);
		console.log("Some random text");
		index = 0;
		slidesHtml(chapter, index);
		downloadNotesHtml();	
	}
	
}
function changeSlide(forward){
	if (index > 0 && !forward){
		index--;
	}
	else if (index < chapters[chapter].slides.length - 1 && forward){
		index++;
	}
	else {
		toastr.info("End of this section");
	}
	console.log(chapters[chapter].name);
	slidesHtml(chapter, index);
	

}	

function slidesHtml(chapterIndex, slideIndex){
	document.getElementById("flashWindow").innerHTML = '<embed width="632" height="460" src=" slides/'+ chapters[chapterIndex].slides[slideIndex].url +'">';	
}

function downloadNotesHtml(){
	var offsetForFirstPositionBeingFullCourse = 1;
	document.getElementById("downloadNotes").innerHTML = '<a href="courseNotes/' + courseNotes[chapter + offsetForFirstPositionBeingFullCourse] + '" style="float:right" download><i class="material-icons grey-text text-lighten-4">cloud_download</i></a>';
}


// Menu bar functions

function buildMenu(){
	for (var chapterIndex = 0; chapterIndex < chapters.length ; chapterIndex++){
		document.getElementById("menubar").innerHTML += '<a href="#" onClick="selectChapter('+ chapters[chapterIndex].chapter +'); return false;" class="waves-effect waves-light btn vula-blue menuButton" id="chapter'+ chapters[chapterIndex].chapter +'">'+ chapters[chapterIndex].name +'</a>';
	}
}

