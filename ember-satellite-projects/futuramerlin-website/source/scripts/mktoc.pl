#!/usr/bin/perl
# mktoc.pl 
#
# Copyright (C) 2013 Pranananda Deva 
#
# mktoc.pl is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
# 
# mktoc.pl is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with mktoc.pl.  If not, see <http://www.gnu.org/licenses/>.

use Storable qw(dclone);
use Data::Dumper;
use Getopt::Std;
use vars qw/ %opt /;

# mktoc
sub doc {
    print STDERR << "EOF";
mktoc.pl creates a doubly-linked table of contents from a HTML files.
The input is STDIN, the output goes to STDOUT. The command line
syntax is:
    ./mktoc.pl < input.html > output.html

Do NOT use the same file name for the input and output file.

You will need to make the script executable before using it, or on Window
you will have to use the follow command line syntax (after you download
perl from http://www.perl.org) :
    perl mktoc.pl  < input.html > output.html

The entries in the table of contents come from the contents between 
a header tag and its end tag, i.e. <h1>...</h1>, <h2>...</h2>,
<h3>...</h3>, and <h4>...</h4>. 

The input ought to be run through tidy before using this script,
though this is not exactly necessary. This script won't work if there
are multiple header tags per line, for example:
    <h1>Hello</h1><h2>This</h2><h3>Is a Test</h3>  <!-- INCORRECT -->

mktoc.pl will also not produce good results if header tags were used
for uses other than demarcating the beginning of a chapter or section.
If header tags are used purely for formatting rather than document
structure, the generated table of contents will be nonsensical.

mktoc.pl can be run multiple times on the same input file, that is,
if you did the following:
    ./mktoc.pl < input.html > output.html
    ./mktoc.pl < output.html > newoutput.html
mktoc.pl can remove the traces of previous runs before applying 
it changes.

Also, traces of previous runs of mktoc.pl can be removed as follows:
    ./mktoc.pl -c < input.html > output.html
    
If a header has subheaders after it, those subheader will be placed
into a small table of contents after the header declaration in the 
output file.

You can specify where the top level Table of Content goes, by including
the following comment into your HTML input files:
    <!-- toc -->
If you don't have this comment in your HTML input file, the top level
table of contents will go right after the <body> tag.

Each table of contents placed in the output file will be surrounded
by a <nav class="toc">. Each line of the table of contents is a simple
<p> tag. You may want to put an entry into your CSS for those elements,
for example (and you can make your own style up here):
    div.toc { margin: 1em 1.1em; }
    div.toc p { margin: 0; text-indent: 0; }
EOF
    exit;
}

$nl = "\n";		# new line terminator
$hastocmarker = 0;	# found a HTML comment <!-- toc -->
%hash = ();		# empty hash
%titlemap = ();		# maps header content string to matching hash
$topheaders;		# ref to array of refs to h1 hashes

# setnl
#   set the newline terminator to windows format
sub setnl {
    $nl = "\r\n";
    $/ = $nl;
}

# h1_new
#   $str is the contents of the <h1> tag
#   $this is the hash that gets returned
#   creates a new h1 hash, saves $str, saves $curh1
sub h1_new {
    my $str = $_[0];
    my $num = $_[1];
    my $this = dclone( \%hash );
    my @child = ();
    $this->{"child"} = dclone( \@child );
    $this->{"val"} = $str;
    $this->{"num"} = $num;
    $this->{"type"} = "h1";
    return $this;
}

# h4_print print STDERRs for debugging a h3 hash
sub h4_print {
    my $this = $_[0];
    print STDERR "            type:", $this->{"type"}, "$nl";
    print STDERR "            val:",  $this->{"val"};
    print STDERR "            num:",  $this->{"num"},  "$nl";
}

# h3_print print STDERRs for debugging a h3 hash
sub h3_print {
    my $this = $_[0];
    my $h4;
    my $arr = $this->{"child"};
    print STDERR "        type:", $this->{"type"}, "$nl";
    print STDERR "        val:",  $this->{"val"};
    print STDERR "        num:",  $this->{"num"},  "$nl";
    foreach $h4 ( @{$arr} ) {
        h4_print($h4);
    }
}

# h2_print print STDERRs for debugging a h2 hash
sub h2_print {
    my $this = $_[0];
    my $h3;
    my $arr = $this->{"child"};
    print STDERR "    type:", $this->{"type"}, "$nl";
    print STDERR "    val:",  $this->{"val"};
    print STDERR "    num:",  $this->{"num"},  "$nl";
    foreach $h3 ( @{$arr} ) {
        h3_print($h3);
    }
}

# h1_print print STDERRs for debugging a h1 hash
sub h1_print {
    my $this = $_[0];
    my $h2;
    my $len;
    my $arr;
    print STDERR "type:", $this->{"type"}, "$nl";
    print STDERR "val:",  $this->{"val"};
    print STDERR "num:",  $this->{"num"},  "$nl";
    $arr = $this->{"child"};

    foreach $h2 ( @{$arr} ) {
        h2_print($h2);
    }
}

# h2_new
#   $str is the contents of the <h2> tag
#   $this is the hash that gets returned
#   creates a new h2 hash, saves $str, saves "$curh1.$curh2"
sub h2_new {
    my $str = $_[0];
    my $num = $_[1];
    my $this = dclone( \%hash );
    my @child = ();
    $this->{"child"} = dclone( \@child );
    $this->{"val"} = $str;
    $this->{"num"} = $num;
    $this->{"type"} = "h2";
    return $this;
}

# h3_new
#   $str is the contents of the <h3> tag
#   $this is the hash that gets returned
#   creates a new h3 hash, saves $str, saves "$curh1.$curh2.$curh3"
sub h3_new {
    my $str = $_[0];
    my $num = $_[1];
    my $this = dclone( \%hash );
    $this->{"child"} = dclone( \@child );
    $this->{"val"} = $str;
    $this->{"num"} = $num;
    $this->{"type"} = "h3";
    return $this;
}

# h4_new
#   $str is the contents of the <h4> tag
#   $this is the hash that gets returned
#   creates a new h4 hash, saves $str, saves "$curh1.$curh2.$curh3.$curh4"
sub h4_new {
    my $str = $_[0];
    my $num = $_[1];
    my $this = dclone( \%hash );
    $this->{"val"} = $str;
    $curh4++;
    $this->{"num"} = "$num.$curh4";
    $this->{"type"} = "h4";
    return $this;
}

sub getendtag {
    my $tag = $_[0];
    my $lineno = $_[1];
    my $savelineno = $$lineno;
    my $starttag = "<".$tag;
    my $endtag = "</".$tag.">";
    $save = $_;
    until (/$endtag/i) {
        $_ = <STDIN>;
        die "line: $savelineno: missing end tag $tag$nl" if ! $_;
        die "line: $savelineno: missing end tag $tag$nl" if /$starttag/i;
        $$lineno++;
        chomp $save;
        $save .= " ".$_;
    }
    $_ = $save;
}

sub skipoldtoc {
    $savelineno = $$lineno;
    until (/<\/nav>/) {
        $_ = <STDIN>;
        die "line: $savelineno: missing end tag </nav>$nl" if ! $_;
        $$lineno++;
    }
    $_ = <STDIN> if $_;
}

# cleanold
# remove traces of previous runs of mktoc.pl
sub cleanold {
    s/<a href="#b_[0-9.]*" id="a_[0-9.]*">(.*?)<\/a>/\1/;
}

# getheaders
# reads through STDIN, and creates hashes for h1, h2, h3, h4 tags
# returns a ref to an array of h1 hashes.
# also maintains titlemap, to go from header content to appropriate hash
sub getheaders {
    my @headers = ();
    my $myh1 = 0;
    my $myh2 = 0;
    my $myh3 = 0;
    my $len, $num;
    my $curh1 = 0;    #incremented every time a <h1> is found
    my $curh2 = 0;    #incremented every time a <h2> is found
    my $curh3 = 0;    #incremented every time a <h3> is found
    my $curh4 = 0;    #incremented every time a <h4> is found
    my $h1, $h2, $h3, $h4, $arr;
    my $lineno = 0;
    my $initnl = 0;

    while (<STDIN>) {
        $lineno++;
        if (!$initnl) {
            setnl if (/\r\n/);
            $initnl = 1;
        }
        skipoldtoc \$lineno if (/<nav class="toc">/);
        if (/<h1/i) {
            getendtag "h1", \$lineno unless (/<\/h1/i);
            $curh1++;
            $num = $curh1;
            cleanold;
            $h1 = h1_new( $_, $num );
            push @headers, $h1;
            $myh1 = $h1;
            $titlemap{"$num $_"} = $h1;
            $curh2 = 0;
            $curh3 = 0;
            $curh4 = 0;
            $myh2 = 0;
            $myh3 = 0;
        }
        if (/<h2/i) {
            getendtag "h2", \$lineno unless (/<\/h2/i);
            $curh2++;
            $num = "$curh1.$curh2";
            cleanold;
            $h2 = h2_new( $_, $num );
            if ($myh1) {
                $arr = $myh1->{"child"};
                push @{$arr}, $h2;
            }
            $myh2 = $h2;
            $titlemap{"$num $_"} = $h2;
            $curh3 = 0;
            $curh4 = 0;
            $myh3 = 0;
        }
        if (/<h3/i) {
            getendtag "h3", \$lineno unless (/<\/h3/i);
            $curh3++;
            $num = "$curh1.$curh2.$curh3";
            cleanold;
            $h3 = h3_new( $_, $num );
            if ($myh2) {
                $arr = $myh2->{"child"};
                push @{$arr}, $h3;
            }
            $myh3 = $h3;
            $titlemap{"$num $_"} = $h3;
            $curh4 = 0;
        }
        if (/<h4/i) {
            getendtag "h4", \$lineno unless (/<\/h4/i);
            $curh4++;
            $num = "$curh1.$curh2.$curh3.$curh4";
            cleanold;
            $h4 = h4_new( $_, $num );
            if ($myh3) {
                $arr = $myh3->{"child"};
                push @{$arr}, $h4;
            }
            $titlemap{"$num $_"} = $h4;
        }
        if (/<!-- toc -->/i) {    # found a table of contents marker
            $hastocmarker = 1;
        }
    }
    return \@headers;
}

# printh1
#
#   $headers is a ref to an array of refs to hash
#   $tag is the current header tag, i.e. "h1", "h2", "h3", or "h4"
#   $title is an option "Table of Contents" header label
#
# prints a div tag around the array, and for each element of the array
# print a line of the table of contents
sub printh1 {
    my $headers = $_[0];
    my $tag = $_[1];
    my $title = $_[2];
    my $h1;
    my $len = @$headers;

    return if ( !$len );
    print "<nav class=\"toc\">$nl";
    print $title if $title;
    foreach $h1 (@$headers) {
        my $val = $h1->{"val"};
        my $num = $h1->{"num"};
        $val =~ s/<$tag.*?>(.*)<\/$tag>.*/$1/i;
        $val = $1;
        chomp $val;
        print "<p id=\"b_$num\"><a href=\"#a_$num\">$val<\/a><\/p>$nl";
    }
    print "</nav>$nl";
}

# printh2
#
#   $h1 is a ref to a h1 hash
#   $tag is the current header tag, i.e. "h1", "h2", "h3", or "h4"
#
# extracts the child array ref and calls printh1
sub printh2 {
    my $h1 = $_[0];
    my $tag = $_[1];
    my $title = $_[2];
    my $child = $h1->{"child"};
    printh1 $child, $tag, $title;
}

# printh3
#
#   $h2 is a ref to a h2 hash
#   $tag is the current header tag, i.e. "h1", "h2", "h3", or "h4"
#
# extracts the child array ref and calls printh1
sub printh3 {
    my $h2 = $_[0];
    my $tag = $_[1];
    my $child = $h2->{"child"};
    printh1 $child, $tag, 0;
}

# printtoplevel
#
#   $headers is a ref to an array of refs to hash
#
# If there is only 1 h1, get the h1, and print its children
# otherwise, print all the h1s in the array
sub printtoplevel {
    my $headers = $_[0];
    my $len = @$headers;
    if ( $len == 1 ) {
        my $h1 = $headers->[0];
        printh2 $h1, "h2", "<h2>Table of Contents<\/h2>$nl";
    }
    elsif ( $len > 1 ) {
        printh1 $headers, "h1", "<h1>Table of Contents<\/h1>$nl";
    }
}

# addtoc
#
#   $headers is a ref to an array of refs to hash
#
# In a second pass of STDIN, replace all the <h1>, <h2>, <h3>, and <h4>
# tags, adding an id="" attribute and a <a>tag that points back to its
# parent tag.
sub addtoc {
    my $headers = $_[0];
    my $h1, $h2, $h3, $h4, $num;
    my $len = @$headers;
    my $curh1 = 0;           #incremented every time a <h1> is found
    my $curh2 = 0;           #incremented every time a <h2> is found
    my $curh3 = 0;           #incremented every time a <h3> is found
    my $curh4 = 0;           #incremented every time a <h4> is found
    my $lineno = 0;

    while (<STDIN>) {
        $lineno++;
        if ( $hastocmarker == 1 ) {
            if (/<!-- toc -->/i) {
                print;
                printtoplevel $headers;
                next;
            }
        }
        else {
            if (/<body/i) {    # if there is no TOC marker, put top
                print;        # level toc after body tag
                printtoplevel $headers;
                next;
            }
        }
        skipoldtoc \$lineno if (/<nav class="toc">/);
        if (/<h1/i) {
            getendtag "h1", \$lineno unless (/<\/h1/i);
            $curh1++;
            $num = $curh1;
            cleanold;
            $h1 = $titlemap{"$num $_"};
            die "line $lineno: $num $_ not found$nl" unless defined $h1;
            s/<h1(.*?)>(.*?)<\/h1>/<h1$1><a href="#b_$num" id="a_$num">$2<\/a><\/h1>/i;
            print;
            printh2 $h1, "h2", 0 if ( $len > 1 );
            $curh2 = 0;
            $curh3 = 0;
            $curh4 = 0;
            next;
        }
        if (/<h2/i) {
            getendtag "h2", \$lineno unless (/<\/h2/i);
            $curh2++;
            $num = "$curh1.$curh2";
            cleanold;
            $h2 = $titlemap{"$num $_"};
            die "line $lineno: $num $_ not found$nl" unless defined $h2;
            $num = $h2->{"num"};
            s/<h2(.*?)>(.*?)<\/h2>/<h2$1><a href="#b_$num" id="a_$num">$2<\/a><\/h2>/i;
            print;
            printh2 $h2, "h3", 0;
            $curh3 = 0;
            $curh4 = 0;
            next;
        }
        if (/<h3/i) {
            getendtag "h3", \$lineno unless (/<\/h3/i);
            $curh3++;
            $num = "$curh1.$curh2.$curh3";
            cleanold;
            $h3 = $titlemap{"$num $_"};
            die "line $lineno: $num $_ not found$nl" unless defined $h3;
            $num = $h3->{"num"};
            s/<h3(.*?)>(.*?)<\/h3>/<h3$1><a href="#b_$num" id="a_$num">$2<\/a><\/h3>/i;
            print;
            printh2 $h3, "h4", 0;
            $curh4 = 0;
            next;
        }
        if (/<h4/i) {
            getendtag "h4", \$lineno unless (/<\/h4/i);
            $curh4++;
            $num = "$curh1.$curh2.$curh3.$curh4";
            cleanold;
            $h4 = $titlemap{"$num $_"};
            die "line $lineno: $num $_ not found$nl" unless defined $h4;
            $num = $h4->{"num"};
            s/<h4(.*?)>(.*?)<\/h4>/<h4$1><a href="#b_$num" id="a_$num">$2<\/a><\/h4>/i;
            print;
            next;
        }
        print;
    }
}

sub cleanup {
    my $lineno = 0;

    while (<STDIN>) {
        $lineno++;
        skipoldtoc \$lineno if (/<nav class="toc">/);
        if (/<h1/i) {
            getendtag "h1", \$lineno unless (/<\/h1/i);
            cleanold;
        }
        if (/<h2/i) {
            getendtag "h2", \$lineno unless (/<\/h2/i);
            cleanold;
        }
        if (/<h3/i) {
            getendtag "h3", \$lineno unless (/<\/h3/i);
            cleanold;
        }
        if (/<h4/i) {
            getendtag "h4", \$lineno unless (/<\/h4/i);
            cleanold;
        }
        print;
    }
    exit 0;
}

sub usage {
    print STDERR << "EOF";
usage: $0 [-hd]
    -h	usage
    -m	show documentation
    -c	remove traces of this script from input
example: 
    $0 < input.html > output.html
    $0 -c < input.html > output.html

EOF
    exit;
}

sub init {
    my $opt_string = "cdhmw";
    getopts( "$opt_string", \%opt ) or usage;
    usage if $opt{h};
    doc   if $opt{m};
    setnl if $opt{w};
    cleanup if $opt{c};
}

# main - getheaders, reset STDIN, and modify header tags

init;
$topheaders = getheaders();
if ($opt{d}) {
    foreach (@$topheaders) {
	h1_print $_;
    }
}
seek STDIN, 0, 0;
addtoc $topheaders;
