#help from http://stackoverflow.com/questions/1064481/how-to-wildcard-include-jar-files-when-compiling http://stackoverflow.com/questions/2356443/building-java-package-javac-to-all-files  https://www.gnu.org/software/make/manual/html_node/Shell-Function.html http://stackoverflow.com/questions/194725/how-can-i-make-the-find-command-on-os-x-default-to-the-current-directory http://stackoverflow.com/questions/17548854/difference-between-mac-find-and-linux-find http://www.devin.com/cruft/javamakefile.html http://jwrr.com/content/Gnu-Makefile-Examples/ http://stackoverflow.com/questions/17548854/difference-between-mac-find-and-linux-find 
JAVAC=javac
sources = $(wildcard *.java)
classes = $(sources:.java=.class)

$(shell find ./ -name "*.java" > sources.txt)
$(shell javac -cp .:`find ./ -name "*.jar" | tr "\n" ":"` @sources.txt)
$(shell find ./ -name "*Test.class" > classes.txt)
$(shell cd src/test/; java -cp . com.futuramerlin.ember.Bootstrap)

all:
	$(classes)

clean:
	$(shell cd src; find . -type f -name "*.class" -delete)

%.class: %.java
	$(JAVAC) $<