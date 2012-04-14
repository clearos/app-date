
Name: app-date
Epoch: 1
Version: 1.1.0
Release: 1%{dist}
Summary: Date and Time
License: GPLv3
Group: ClearOS/Apps
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = 1:%{version}-%{release}
Requires: app-base

%description
The time zone and clock synchronization tool.

%package core
Summary: Date and Time - Core
License: LGPLv3
Group: ClearOS/Libraries
Requires: app-base-core
Requires: app-network-core
Requires: app-tasks-core
Requires: ntpdate >= 4.2.4p8

%description core
The time zone and clock synchronization tool.

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/date
cp -r * %{buildroot}/usr/clearos/apps/date/

install -D -m 0644 packaging/app-date.cron %{buildroot}/etc/cron.d/app-date
install -D -m 0644 packaging/date.conf %{buildroot}/etc/clearos/date.conf
install -D -m 0644 packaging/filewatch-date-network.conf %{buildroot}/etc/clearsync.d/filewatch-date-network.conf
install -D -m 0755 packaging/timesync %{buildroot}/usr/sbin/timesync

%post
logger -p local6.notice -t installer 'app-date - installing'

%post core
logger -p local6.notice -t installer 'app-date-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/date/deploy/install ] && /usr/clearos/apps/date/deploy/install
fi

[ -x /usr/clearos/apps/date/deploy/upgrade ] && /usr/clearos/apps/date/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-date - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-date-core - uninstalling'
    [ -x /usr/clearos/apps/date/deploy/uninstall ] && /usr/clearos/apps/date/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/date/controllers
/usr/clearos/apps/date/htdocs
/usr/clearos/apps/date/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/date/packaging
%exclude /usr/clearos/apps/date/tests
%dir /usr/clearos/apps/date
/usr/clearos/apps/date/deploy
/usr/clearos/apps/date/language
/usr/clearos/apps/date/libraries
/etc/cron.d/app-date
%config(noreplace) /etc/clearos/date.conf
/etc/clearsync.d/filewatch-date-network.conf
/usr/sbin/timesync
