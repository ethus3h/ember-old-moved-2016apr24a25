JFLAGS = -g
JC = javac
.SUFFIXES: .java .class
.java.class:
	$(JC) $(JFLAGS) $*.java

CLASSES = src/main/com/futuramerlin/ember/DataType/TreeHW2/MyF1To8.java\
src/main/com/futuramerlin/ember/DataType/TreeHW2/MyTree.java\
src/main/com/futuramerlin/ember/DataType/TreeHW2/MyTreeNode.java\
default: classes

classes: $(CLASSES:.java=.class)

clean:
	$(RM) *.class