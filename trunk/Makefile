JFLAGS = -g
JC = javac
.SUFFIXES: .java .class
.java.class:
	$(JC) $(JFLAGS) $*.java

CLASSES = src/main/com/futuramerlin/ember/DataProcessor/DataProcessor.java\
src/main/com/futuramerlin/ember/DataProcessor/HashGenerator.java\
src/main/com/futuramerlin/ember/DataType/DataType.java\
src/main/com/futuramerlin/ember/DataType/HashEntry.java\
src/main/com/futuramerlin/ember/DataType/HashSet.java\
src/main/com/futuramerlin/ember/SafeData.java\
src/main/com/futuramerlin/ember/Server/FrontEndServer/FrontEndServer.java\
src/main/com/futuramerlin/ember/Server/JettyServer.java\
src/main/com/futuramerlin/ember/Server/MetaServer/MetaServer.java\
src/main/com/futuramerlin/ember/Server/RecordServer/RecordServer.java\
src/main/com/futuramerlin/ember/Server/Server.java\
src/main/com/futuramerlin/ember/Throwable/CorruptedSafeDataException.java\
src/main/com/futuramerlin/ember/Throwable/hashSetItemNotFoundException.java\
src/main/com/futuramerlin/ember/Throwable/hashSetNullArgumentException.java\
src/test/com/futuramerlin/ember/DataProcessor/DataProcessorTest.java\
src/test/com/futuramerlin/ember/DataProcessor/HashGeneratorTest.java\
src/test/com/futuramerlin/ember/DataType/DataTypeTest.java\
src/test/com/futuramerlin/ember/DataType/HashEntryTest.java\
src/test/com/futuramerlin/ember/DataType/HashSetTest.java\
src/test/com/futuramerlin/ember/SafeDataTest.java\
src/test/com/futuramerlin/ember/Server/FrontEndServer/FrontEndServerTest.java\
src/test/com/futuramerlin/ember/Server/MetaServer/MetaServerTest.java\
src/test/com/futuramerlin/ember/Server/RecordServer/RecordServerTest.java\
src/test/com/futuramerlin/ember/Server/ServerTest.java\

default: classes

classes: $(CLASSES:.java=.class)

clean:
	$(RM) *.class