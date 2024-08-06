# Shlexer
A web app for lexing Shakespeare's plays. 

Shlexer extracts the words from a play text, counts each occurence of that word, and then displays a table with that information. You can also limit the scope of the lexing by either a character, an act, a scene, or some combination of all of the above.

"Why do this," you ask? Some actors and directors will take a high frequency of a certain word as an indicator that it has some special meaning in a play, or a characters' use of that word as a guide to understanding that character. Counting that sort of info is something computers should be good at.

This is a JavaScript implementation of a program I initially wrote in Ruby when I started grad school in 2009, which was then designed to work with bare text files.

This is currently only designed to work with the TEI Simple encoded XML texts published by the Folger Shakespeare Library. You can download those from [(https://www.folger.edu/explore/shakespeares-works/download/](https://www.folger.edu/explore/shakespeares-works/download/)

The latest live version of Shlexer lives at [https://tonytambasco.com/software/release/shlexer.php](https://tonytambasco.com/software/release/shlexer.php). It mostly works, but use at your own risk. 
