package com.futuramerlin.ember.DataType;

import com.futuramerlin.ember.Throwable.hashSetItemNotFoundException;
import com.futuramerlin.ember.Throwable.hashSetNullArgumentException;

import java.util.AbstractCollection;
import java.util.Iterator;
import java.util.Set;

/**
 * Created by elliot on 14 September 14.
 */
public class HashSet<AnyType> extends AbstractCollection<AnyType> implements Set<AnyType> {
    HashEntry[] array;
    public int currentIndex = 0;

    @Override
    public Iterator<AnyType> iterator() {
        return null;
    }

    public void clear() {
    }

    @Override
    public boolean contains(Object a) {
        //  try {
        int position = 0;
        try {
            position = findPos(a);
        } catch (Exception e) {
            throw new RuntimeException();
        } catch (hashSetNullArgumentException e) {
            throw new RuntimeException();
        } catch (hashSetItemNotFoundException e) {
            throw new RuntimeException();
        }
        return this.isActive(array, position);
      /*  } catch (hashSetItemNotFoundException e) {
            e.printStackTrace();
        }*/
        //return true;
    }

    @Override
    public boolean add(AnyType a) {
        this.array[this.currentIndex] = new HashEntry(a, true);
        System.out.println("Added " + a + " at position " + this.currentIndex);
        this.increment();
        return false;
    }

    @Override
    public int size() {
        return 0;
    }

    public void allocateArray(int arraySize) {
        this.array = new HashEntry[arraySize];
    }

    public int findPos(Object a) throws hashSetItemNotFoundException, hashSetNullArgumentException {
        if(null==a) {throw new hashSetNullArgumentException();}
        throw new hashSetItemNotFoundException();

       /* int found = -1;
        for (int i = 0; i < (this.array.length-1); i++) {
            System.out.println("At index " + i);
            if (this.array[i].equals(a)) {
                found = i;
            }
            System.out.println("At index " + i+" again");

        }
        if (found == -1) {
            throw new hashSetItemNotFoundException();
        }*/
        //help from http://stackoverflow.com/questions/3790142/java-equivalent-of-pythons-rangeint-int
      /*  IntStream.range(0, this.currentIndex).forEach(
                n -> {
                    if (this.array[n].equals(a)) {
                        found[0] = n;
                    }
                }
        );*/
        //return found;
        //return 0;
    }

    public boolean isActive(HashEntry[] hashEntryArray, int positionInHashTable) {
        return hashEntryArray[positionInHashTable] != null && hashEntryArray[positionInHashTable].isActive;
    }

    public void increment() {
        this.currentIndex++;
    }

}
