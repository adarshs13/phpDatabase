package nicetime;

#dummy constructor
sub new {
	my($self)={};
	bless($self);
	return $self;
}

sub parse_date {
	my ($me, $extended, $format) = @_;
	my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time + $extended);
	#0-59,0-59,0-23,1-31,0-11,YYYY,0-6,0-365,is Daylight Saving Time true/false
	$mon += 1;
	$year += 1900; #years since 1900 so add 1900 to get current year
	my %months = qw(1 Jan 2 Feb 3 Mar 4 Apr 5 May 6 Jun 7 Jul 8 Aug 9 Sep 10 Oct 11 Nov 12 Dec);
	my %weekdays = qw(0 Sun 1 Mon 2 Tue 3 Wed 4 Thu 5 Fri 6 Sat);
	
	#format
	my %format_code = ('sec'=>$sec, 'min'=>$min, 'hour'=>$hour, 'mday'=>$mday, 'nicemon'=>$months{$mon}, 'mon'=>$mon, 'year'=>$year, 'nicewday'=>$weekdays{$wday}, 'wday'=>$wday, 'yday'=>$yday, 'isdst'=>$isdst);
	my @fixedorder = ('nicemon', 'nicewday', 'sec', 'min', 'hour', 'mday', 'mon', 'year', 'wday', 'yday', 'isdst');
	foreach my $key (@fixedorder) {
		my $value = $format_code{$key};
		if ( $value < 10 and $value !~ m/[a-zA-Z]+/) {
			$value = "0" . $value;
		}
		$format =~ s/$key/$value/g;
	}
	return "$format";
}

return 1;

__END__
__DATA__
