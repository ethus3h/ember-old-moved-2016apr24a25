compile() {
	echo "Trying to build $1..."
	javac -cp ../../lib ./$1.java
	jar cfe $1.jar $1 .
	echo "Your $1 is now ready in $1.jar."
}

