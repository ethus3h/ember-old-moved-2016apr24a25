NAME="StdioClient"
#JAVAC=javac
#sources = $(wildcard *.java)
#classes = $(sources:.java=.class)

all:
	$(info Trying to build $(NAME)...)
	#$(classes)
	#$(shell javac -cp . ./$(NAME).java)
	#$(shell find ./ -name "*.java" > sources.txt)
	#$(shell javac -cp .:`find ./ -name "*.jar" | tr "\n" ":"` @sources.txt)
	#$(shell jar cf $ITEMTOBUILD.jar .)
	#$(shell echo "Your $ITEMTOBUILD is now ready in $ITEMTOBUILD.jar.")

clean:
	$(shell cd src; find . -type f -name "*.class" -delete)

#%.class: %.java
#	$(JAVAC) $<