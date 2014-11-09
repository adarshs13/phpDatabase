#Library
use threads;
use nicetime;
my $nicetime = nicetime->new();

#global
my @drivesundertest;
my $PID = $$;
my $MPID;

#User
my @drivestomonitor = (e,f,g,h,i,j,k,l);
my $monitorinterval = 30;
my $reportinterval = 30;
my $progressbarlength = 10;

#Handle Signals
#$SIG{ SIGNAME } = sub { };
@SIG{qw( INT TERM HUP QUIT ABRT __DIE__ )} = \&DIECLEAN;

#allow only one executing instance
my $filename = $0;
my $rundir = "/var/run/";
my $runfile = $rundir.$filename.".pid";
unless (-e $runfile) {
	system("echo $PID > $runfile");
}

#Main loop which will watch for drives
system("setterm -blank 0");
system("clear");
while (1) {
	$MPID = qx"cat $runfile";
	chomp($MPID);
	system("clear");
	print "Welcome to HDD Wipe Program. Please insert drive to start.\n";
	print "When complete please remove drive until it is no longer listed on the screen.\n";
	print "Current time: ".$nicetime->parse_date(0, "nicewday year\/mon\/mday hour\:min\:sec")."\n";
	if ($PID != $MPID) {
		print "Stats Only Mode - Master program [$MPID]\n";
	}
	print "\n";
	my $driverunning = 0;
	my @completedrives; #we want it clear for each iteration
	my @sfarr = split("\n", qx"sfdisk -q -s 2>&1 | grep sd"); #quickly capture sf output for all drives
	#OUTPUT "/dev/sde: 488386584"
	foreach my $drive (@drivestomonitor) {
		#Drive variables
		my $driveundertest = 0; #drive currently being tested
		my $drivesize = 0;
		my $sfoutput = "Detached"; #sfoutput for drive
		foreach my $line (@sfarr) {
			if ($line =~ m/sd$drive/) {
				$sfoutput = $line;
				$sfoutput =~ m/\/dev\/sd$drive\:\s+(\d+)/;
				$drivesize = $1 * 1024;
			}
		}
		
		#Is drive already running? Progress. Complete Report.
		for (my $index = 0; $index < @drivesundertest; $index++) {
			my $undertest = $drivesundertest[$index];
			if ($drive eq $undertest) {
				$driverunning = 1;
				$driveundertest = 1;
				my $processoutput = qx"ps -af | grep -v grep | grep dd | grep sd$drive";
				if ($processoutput !~ m/dd/) {
					#dd is done watch for remove
					if ($sfoutput =~ m/Detached/) {
						#remove drive from array
						delete $drivesundertest[$index];
						$index--;
					} else {
						#print completed stats on screen
						$ret = qx"tail -n 1 ddout_$drive.log";
						$ret =~ m/(\d+)\s+bytes.+copied, ([0-9.]+) seconds, ([0-9.]+) ([a-zA-Z\/]+)/;
						my $Bsize = $1;
						my $seconds = $2;
						my $size = "B";
						while ($Bsize >= 1024) {
							$Bsize = $Bsize / 1024;
							if ($size =~ m/^B/) {
								$size = "KB";
							} elsif ($size =~ m/KB/) {
								$size = "MB";
							} elsif($size =~ m/MB/) {
								$size = "GB";
							} elsif($size =~ m/GB/) {
								$size = "TB";
							} elsif($size =~ m/TB/) {
								$size = "PB";
							}
						}
						$Bsize = int($Bsize) . " $size";
						my $timecounter = 0;
						while ($seconds >= 60) {
							$seconds = $seconds / 60;
							$timecounter++;
						}
						$seconds = sprintf("%.2f", $seconds);
						if ($timecounter == 0) {
							$seconds .= " seconds";
						} elsif ($timecounter == 1) {
							$seconds .= " minutes";
						} else {
							$seconds .= " hours";
						}
						my $ddout = qx"cat ddout_$drive.log | grep seconds";
						my $ddavg = 0;
						my $count = 0;
						foreach my $line (split("\n",$ddout)) {
							$count++;
							$line =~ m/\d+\s+bytes.+copied, [0-9.]+ seconds, ([0-9\.]+) ([a-zA-Z\/]+)/;
							my $rate = $1;
							if ($2 =~ m/KB\/s/) {
								$ddavg += $rate / 1024;
							} elsif ($2 =~ m/MB\/s/) {
								$ddavg += $rate;
							} elsif ($2 =~ m/GB\/s/) {
								$ddavg += $rate * 1024;
							}
						}
						$ddavg /= $count;
						$ddavg = sprintf("%.2f", $ddavg);
						push(@completedrives, $drive);
						print "COMPLETED Drive sd$drive [$Bsize] in $seconds at an avgerage speed $ddavg MB/s\n";
					}
				} else {
					#dd still running
					$processoutput =~ m/\w+\s+(\d+)\s+.+/;
					my $pid = $1;
					if ($PID == $MPID) {
						system("kill -USR1 $pid");
					}
					$ret = qx"tail -n 1 ddout_$drive.log";
					$ret =~ m/(\d+)\s+bytes.+copied, ([0-9.]+) seconds, ([0-9.]+) ([a-zA-Z\/]+)/;
					my $bytes = $1;
					my $duration = $2;
					my $speed = $3;
					my $nicespeed = $speed . " $4";
					my $completed = $bytes / $drivesize;
					my $completedpercent = int($completed * 100);
					my $completion = ($duration / $completed) - $duration ;
					my $progressbar = "|";
					for (my $i = 0; $i < $progressbarlength; $i++){
						my $completed = ($i / $progressbarlength) * 100;
						if ($completed <= $completedpercent and $completed != 0) {
							$progressbar .= "=";
						} elsif ($completed != 0) {
							$progressbar .= " ";
						}
					}
					$progressbar .= " |";
					my $size = "B";
					while ($drivesize >= 1024) {
						$drivesize /= 1024;
						if ($size =~ m/^B/) {
							$size = "KB";
						} elsif ($size =~ m/KB/) {
							$size = "MB";
						} elsif($size =~ m/MB/) {
							$size = "GB";
						} elsif($size =~ m/GB/) {
							$size = "TB";
						} elsif($size =~ m/TB/) {
							$size = "PB";
						}
					}
					$drivesize = int($drivesize);
					if ($completedpercent < 10) {
						$completedpercent = "0" . $completedpercent;
					}
					print "/dev/sd$drive $drivesize $size at $completedpercent% $progressbar $nicespeed. Expected at ".$nicetime->parse_date($completion, "nicemon mday hour:min")."\n";
				}
			}
		}
		
		#Is there a drive attached? Is DD already running? If not start DD.
		if ($driveundertest == 0) {
			if ($sfoutput =~ m/sd$drive/i) {
				push (@drivesundertest, $drive); #dont need to check for duplicate as if the drive is already undertest it wont go here to be added
				my $processoutput = qx"ps -af | grep -v grep | grep dd | grep sd$drive";
				if ($processoutput !~ m/dd/ and $PID == $MPID) {
					$driverunning = 1;
					system ("nohup dd if=/dev/zero of=/dev/sd$drive bs=1M > ddout_$drive.log 2>&1& > /dev/null");
				}
			}
		}
	}
	
	#Blink finished drives
	foreach my $drive (@completedrives) {
		async {
			system("/usr/AmiNas/cli blink -t pd -d /dev/sd$drive -n 0 raid 2>&1 > /dev/null"); #was printing to screen number of disks connected to system, sent output to null
		}; #must put ; at the end of async call.
	}
	
	#is the main program still running?
	my $isrunning = 0;
	my @running = split("\n", qx"ps ax | grep -v grep | grep $filename");
	foreach my $run (@running) {
		$run =~ m/(\d+).+/;
		my $tMPID = $1;
		if ($tMPID == $MPID) {
			$isrunning = 1;
		}
	}
	if ($isrunning == 0) {
		system("echo $PID > $runfile");
	}
	
	#Sleep
	if ( $driverunning == 0 ) {
		sleep 5;
	} else {
		sleep $monitorinterval;
	}
}

sub DIECLEAN {
	if ($PID == $MPID) {
		unlink $runfile;
	}
	die;
}
