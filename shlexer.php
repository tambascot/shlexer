	<?
	
	# Snapshot of a word counting parser currently in development at https://jsfiddle.net/tambascot/75gfqubv/361/
	
	?>
<html>

<? 
	$VERSION = "0.3a"; 	
?>

<head>

<title>Shlexer - A Shakespeare Lexer</title>

<!-- CSS goes here -->
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}

td {
  padding: 1px;
}

#footer {
  font-size: small;
}

#changelog {
  display: none;
}
</style>

<!-- Javascript goes here -->
<script>
// a global hash of words that we encounter in the script that we're analyzing. This is necessary so that we can make the display of the words and their tallys separate from other aspects of the document.
let words_hash = new Map();
	
let debug_array = [];

// a global has of the parsed xmlDoc. This is necessary so that we do not have to fire the countWords function from within one of the functions that sets up the scene and character selection menus. 
var xmlDoc;

// readText(event)
// Reads a file chosen by user into memory
// adapted from https://stackoverflow.com/questions/750032/reading-file-contents-on-the-client-side-in-javascript-in-various-browsers
async function readText(event) {
	const file = event.target.files.item(0)
	const text = await file.text();
	parseXML(text);
}

// parseXML
// Parses XML to do... stuff...? 
// INPUT: the XML text file read into a variable
// adapted from: https://www.w3schools.com/xml/xml_parser.asp
function parseXML(XMLText) {
	let parser;

  	parser = new DOMParser();
  	xmlDoc = parser.parseFromString(XMLText, "text/xml");

	document.getElementById("output").innerHTML =
    	xmlDoc.getElementsByTagName("title")[0].childNodes[0].nodeValue;
    
    createCastSelect();
    createSceneSelect();
    
  	document.getElementById("countWords_button").innerHTML
  		= "<button onclick=\"countWords_wrapper()\">Count Words</button>\n";
}

// createCastSelect
// Extracts the cast list to display as a selection menu
// INPUT: none
// RETURN: none
function createCastSelect() {

	let charSelectList = "<select name='characters' id='characters'>\n <option value='All'>All</option>\n";
	let castList = xmlDoc.getElementsByTagName("role");
  
  	for (let i = 0; i < castList.length; i++) {
    	let name = castList[i].getElementsByTagName('name')[0].textContent;
    
    	charSelectList += "<option value='" + name 
    		+ "'>" + name + "</option>\n";
	}
 
	charSelectList += "</select>\n";
  
   	document.getElementById("cast_list").innerHTML = charSelectList;
}

// createSceneSelect
// creates a selection list of acts and scenes
// INPUT: none
// RETURN: none
function createSceneSelect() {

	let sceneSelectList = "<select name='scenes' id='scenes'>\n <option value='All'>All</option>\n";

	let divList = xmlDoc.getElementsByTagName("div");
  
  	let act;
  
  	for (let i = 0; i < divList.length; i++) {
    
		// becaause we're iterating through the XML doc 
		// in roder, we can assemble scenes as act.scene
		// for clarity.

    	if (divList[i].getAttribute('type') == 'act') {
    		act = divList[i].getAttribute('n'); 	
	      	sceneSelectList += "<option value='" + act 
    			+ "'>" + act + "</option>\n";
    	} else if (divList[i].getAttribute('type') == 'scene') {
    		if (act) {
        		let scene = divList[i].getAttribute('n');

		        sceneSelectList += "<option value='" + act 
        			+ "." + scene
        			+ "'>" + act + "." + scene + "</option>\n";
      		}
    	} else if (divList[i].getAttribute('type') == 'epilogue') {
    		act = divList[i].getAttribute('n');
      
		    sceneSelectList += "<option value='" + act 
    			+ "'>" + act + "</option>\n";
		}
    
	}
  
  	sceneSelectList += "</select>\n";
  
   	document.getElementById("scene_list").innerHTML = sceneSelectList;
}

// countWords
// Count the words in the play by sorting them into a 
// 		hash that tracks their occurences, within the 
//		given parameters.
// INPUT: the act to limit our query to, the scene to limit our query to, and the dramatic character to limit our query to.
// RETURN: none
function countWords(act_limit, scene_limit, dramchar_limit) {

	// clear the hash to prevent the count from accumulating over multiple uses.
	words_hash.clear();
  
  	let words = xmlDoc.getElementsByTagName("w"); 
  
  	for (let x = 0; x < words.length; x++) {
		// the grandparent node contains the name of the speaker.
		let nona = words[x].parentNode.parentNode;
		
		// or the great-grandparent node does.
		let bisnona = words[x].parentNode.parentNode.parentNode;
		let speaker = "";

		// we only want to consider this work if it has a 
		// named speaker. That will either be the grandparent or the great-grandparent
		// of the w element in question.
    	if (nona.nodeName == "sp" && nona.getAttribute("who") != null) {
			// the "who attribute has some trailing chars to slice off"
			speaker = nona.getAttribute("who").slice(1, -4);	
		} else if (bisnona.nodeName == "sp" && bisnona.getAttribute("who") != null) {
			// the "who attribute has some trailing chars to slice off"
			speaker = bisnona.getAttribute("who").slice(1, -4);
		} else if (bisnona.getAttribute("type") === "song") {
			// if the word is in a song, we have to go a bit further into the family tree...
			speaker = bisnona.parentNode.getAttribute("who").slice(1, -4);
		}
		else { continue; }
		
		console.log(speaker);
      
		// the n attribute is the TLN containing the word, which also has the act and scene values.  
		let tln = words[x].getAttribute("n");

		if (speaker != null && tln != null) {

			// if the tln contains "SD," it's a stage direction, so don't count it.
			if (tln.includes("SD") == true) {
				continue;
			}

			// extract the act and scene values from the TLN
			let tln_array = tln.split(".");
			let act = tln_array[0];
			let sce = tln_array[1];

			// if the speaker of the word isn't one we're
			// looking for, look at the next word.
			if (speaker != dramchar_limit && dramchar_limit.toLowerCase() != "all") { 
				continue; 
			}

			// if the word is from an act that we're not 
			// examining, look at the next word.
			if (act !=  act_limit && act_limit.toLowerCase() != "all" ) { 
				continue; 
			}

			// if the word is from a scene that we're not 
			// examining, look at the next word. 
			if (sce != scene_limit && scene_limit.toLowerCase() != "all") { 
				continue; 
			}

			// convert the word to lower case and store in a 
			// more convenient variable. 
			let word = words[x].childNodes[0].nodeValue.toLowerCase();

			// if the word is in the hash, increment its count by 1, otherwise add it to the hash with a value of 1.
			if (words_hash.has(word)) {
				let count = (words_hash.get(word) + 1);
				words_hash.set(word, count);   
			} else {
				words_hash.set(word, 1);
			}
		}
	}
	
	return true;
}

// verifySpeaker
// verify that the speaker of the word in question is one we should include for our totals. 
// INPUT: the speakerName and the character we are limiting our query to.
// RETURN: true or false

function verifySpeaker(speakerName, dramchar_limit) {

	if (dramchar_limit.toLowerCase() == 'all') {
		// if we're doing all speakers, it's verified
		return true;
  	} else if (speakerName.toLowerCase() == dramchar_limit.toLowerCase()) {
  		// if the speakerName is the same as the character we're looking for, it is verified.
    	return true;
  	} else { return false; }
}

// countWords_wrapper
// A function to extract the act/scene and character selection values from their menus and then calling the countWords function using those values.
// INPUT: none
// RETURN: none
async function countWords_wrapper() {

	// aas == act and scene
	let aas = document.getElementById("scenes");
  	let aas_text = aas.options[aas.selectedIndex].text;
  
  
	// split the string into the variables we'll use
	let [act_limit, scene_limit] = aas_text.split(".");
  
  	// If scene_limit variable has no value, set it to "all"
  	if (!scene_limit) { scene_limit = "All"; }

	// get the dramatic character limit parameter

	// dce == dramchar element
	let dce = document.getElementById("characters");
  	let dramchar_limit = dce.options[dce.selectedIndex].text;

  	// call the countWords() function; await for it to finish running before doing anything else

  	const tally = await countWords(act_limit, scene_limit, dramchar_limit);

  	if (tally == true){
  		let sorted_hash = getSortedHash_valueDescending(words_hash);
    	displayResults(sorted_hash);
  	}
}


// Sort the hash with the frequency value descending
// INPUT: the hash to sort
// OUTPUT: a sorted copy of that hash
function getSortedHash_valueDescending(inputHash) {

	const newMap = Array.from(inputHash).sort((a, b) => b[1] - a[1]);
  	const resultHash = new Map(newMap);
  
  	return resultHash;
}

// Sort the hash with the key value ascending
// NOT YET IMPLEMENTED
function getSortedHash_keyAscending(inputHash) {
	return true;
}

// Display the hash as a table
// INPUT: the hash to display
function displayResults(input_hash) {

	console.log("displaying results...");

  	let resultsTable = "<table><tr><th>word</th><th>frequency</th></tr>\n";

  	input_hash.forEach(function callback(value, key) {
    	resultsTable += "<tr>\n  <td>" + key + "</td>\n";
    	resultsTable += "			<td>" + value + "</td>\n</tr>"; 
  	});
  
  	resultsTable += "\n</table>\n";

	document.getElementById("results").innerHTML = resultsTable;
	
	let debugString = "<pre>\nTotal counted: " + debug_array.length + "\n";
	
	debug_array.forEach((element) => debugString += element += "\n");
	
	debugString += "</pre>";
	
	// uncomment for debugging report:
	// document.getElementById("debug").innerHTML = debugString;
	
}
	
</script>

</head>

  <body>

    <h1>
      Shlexer
    </h1>
    
    <h3>
    A <u>Sh</u>akespeare <u>lexer</u>. 
    </h3>
    
    <p>
      Shlexer extracts the words from a play text, counts each occurence of that word, and then displays a table with that information. You can also limit the scope of the lexing by either a character, an act, a scene, or some combination of all of the above. 
    </p>
    
    <p>
    "Why do this," you ask? Some actors and directors will take a high frequency of a certain word as an indicator that it has some special meaning in a play, or a characters' use of that word as a guide to understanding that character. Counting that sort of info is something computers should be good at. 
    </p>
    
    <p>
      This is a JavaScript implementation of a program I initially wrote in Ruby when I started grad school in 2009, which was then designed to work with bare text files. 
    </p>
    
    <p>
      This is currently only designed to work with the TEI Simple encoded XML texts published by the Folger Shakespeare Library. You can download those from <a href="https://www.folger.edu/explore/shakespeares-works/download/"> https://www.folger.edu/explore/shakespeares-works/download/</a>
    </p>

    <input type="file" onchange="readText(event)" />
    <pre id="output"></pre>
    
    <div id="selection_lists">
      <!-- Selection lists to control which characters
          and acts / scenes we examine -->
      <div id="cast_list">
        <!-- to be filled in by javascript -->
      </div>
      
      <div id="scene_list">
        <!-- to be filled in by javascript -->
      </div>
      
      <div id="countWords_button">
        <!-- to be filled in by javascript -->
      </div>
    </div>
    
    <div id="results">
      <!-- The results of the analysis -->
    </div>
	  
	<div id="debug">
		
 	</div>

  <div id="footer">
    <p>
    Shlexer is Copyright &copy; <?php echo date("Y"); ?> by Tony Tambasco, and liscensed under version 3 of the GNU Public License (GPLv3). The text of this license is available online at <a href="https://www.gnu.org/licenses/gpl-3.0.html">https://www.gnu.org/licenses/gpl-3.0.html</a>. The current release of Shlexer is version <?php echo $VERSION; ?> 
    </p>
  </div>
  
  <div id="changelog">
	  <h4>Changelog</h4>
      <ul>
		  <li>
			  Version 0.3a: 8 Aug 2024. Corrected bug that was excluding words that are nested in a speech as part of an &lt;lg&gt;" element. 
		  </li>
		  <li>
			  Version 0.2a: 03 Aug 2024. Corrected bug that was causing stage directions to be counted as spoken words.
		  </li>
		  <li>
			  Version 0.1a: 30 July 2024. Initial alpha release.
		  </li>
			  
      </ul>
  </div>

  </body>

</html>
