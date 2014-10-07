#help from https://gist.github.com/badsector/1395205 http://www.cs.swarthmore.edu/~newhall/unixhelp/javamakefiles.html http://stackoverflow.com/questions/4063863/how-to-write-set-classpath-in-makefile-for-java-in-linux http://stackoverflow.com/questions/5487838/makefile-with-jar-and-package-dependencies http://stackoverflow.com/questions/8561640/make-nothing-to-be-done-for-all
#
# Generic Java makefile
#
 
CLASSES = $(wildcard *.java)
JFLAGS = -g
JC = javac
 
.SUFFIXES:
	.java .class
 
.java.class:
	$(JC) $(JFLAGS) $*.java
 
classes:
	$(CLASSES:.java=.class)

all:
	classes
 
clean:
	$(RM) *.class