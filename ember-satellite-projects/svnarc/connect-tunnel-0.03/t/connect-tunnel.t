use Test::More tests => 3;
use IPC::Open3;
use LWP::UserAgent;
use strict;

{
    local ( *IN, *OUT, *ERR );
    my $pid = open3( \*IN, \*OUT, \*ERR, "$^X -c connect-tunnel" );
    wait;

    local $/ = undef;
    my $errput = <ERR>;
    like( $errput, qr/syntax OK/, "The script compiles" );
}

# NOTE: we do not test the script itself
# but the method it uses to connect to the proxy

SKIP:
{

    # we will test the script with HTTP_PROXY
    skip "No HTTP_PROXY environment variable", 2 unless exists $ENV{HTTP_PROXY};

    # connect to the proxy
    my $ua  = LWP::UserAgent->new( env_proxy => 1 );
    my $req = HTTP::Request->new( CONNECT    => 'http://pause.perl.org:443' );
    my $res = $ua->request($req);

    # the proxy socket
    my $sock = $res->{client_socket};
    isa_ok( $sock, 'IO::Socket::INET', "Got a socket to the proxy" );
    like( $res->code, qr/^[234]\d\d$/, $res->status_line );
}

