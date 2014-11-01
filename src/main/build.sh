#!/bin/bash
echo "Trying to build Ember..."
echo "Cleaning any existing files..."
rm -rf *.class
echo "Done cleaning"
echo "Compiling..."
emcompile() {
	echo "Trying to build module: $1..."
	javac -cp . ./com/futuramerlin/ember/${1/./\/}/${1#*.}.java
	jar cfe $1.jar com.futuramerlin.ember.$1.${1#*.} .
	echo "Your $1 is now ready in $1.jar."
}

emcompile "Client.StdioClient";


echo "Done!"