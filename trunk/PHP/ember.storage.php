<?php
//Storage engine: This script handles storage management for Ember, providing abstract storage management functions for other components.

//Data types: Binary (file), string.
//All data records are DCE documents (Ember is a pure DCE system, unlike Weave). Binary records are encapsulated binary data consisting simply of whatever is chosen by the user, and can contain any binary data. These are parsed elsewhere in the software, or simply returned to the user. String records are also DCE records, 

//Data subtypes: Content, text, number, date, boolean, index ­- These are the conceptual data types.
//Content records are the only subtype of Binary records, and can be whatever the user chooses. Text records are strings expressing generic content (can be layed-out (bounded) or not). Number records are strings expressing numerical values. Date records are strings expressing dates. Booleans strings expressing true/false. Indices are strings expressing lists.


include('d/r/libdce.php');
