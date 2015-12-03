# $Id: Makefile.PL,v 1.2 2003/03/18 14:21:29 book Exp $
use ExtUtils::MakeMaker;

WriteMakefile(
    NAME         => 'connect-tunnel',
    VERSION_FROM => 'connect-tunnel',
    EXE_FILES    => ['connect-tunnel'],
    PREREQ_PM    => {
        'LWP::UserAgent'      => 2,
        'HTTP::Headers::Util' => 0,
    },
    MAN1PODS => { 'connect-tunnel' => '$(INST_MAN1DIR)/connect-tunnel.1' },
);
