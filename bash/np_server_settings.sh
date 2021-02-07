#!/bin/bash

############################################################
### How to use the CLI of "np_server_settings.sh" Script ###
############################################################

### GET Server Settings
# ---------------------
# np_server_settings.sh --device="enp0s3" --command=get 
# -----------------------------------------------------
# ipv4.method:                            manual
# ipv4.dns:                               8.8.8.8
# ipv4.addresses:                         192.168.0.2/24
# ipv4.gateway:                           192.168.0.1

### SET Server Settings
# ---------------------
# np_server_settings.sh --device="enp0s3" --command=set --ipv4=192.168.0.1/24 --dhcp=auto --gateway=192.168.0.1 --dns1=8.8.8.8 --dns2=9.9.9.9
# -------------------------------------------------------------------------------------------------------------------------------------------

### ERRORS AND STATUSES (examples of output messages)
#----------------------------------------------------
# Error: Input key is not found: --some-wrong-key=1
# Connection 'enp0s3' successfully deactivated (D-Bus active path org/freedesktop/NetworkManager/ActiveConnection/1)
# Connection successfully activated (D-Bus active path: /org/freedesktop/NetworkManager/ActiveConnection/4)
# Connection 'enp0s3' deactivation failed : Not authorized to deactivate connections.

device=0
command=0
dhcp=auto

ipv4=0
gateway=0
dns1=0
dns2=0

msg_filter=''

err_code=0

######################################################################
# These two common functions are required to parse input keys/values #
######################################################################

procParmL() 
{ 
   [ -z "$1" ] && return 1 
   if [ "${2#$1=}" != "$2" ] ; then 
      cRes="${2#$1=}" 
      return 0 
   fi
   return 1
} 

while [ 1 ] ; do 
   	if procParmL "--device" "$1" ; then 
    	device="$cRes"
   	elif procParmL "--command" "$1" ; then 
    	command="$cRes"
	elif procParmL "--dhcp" "$1" ; then 
    	dhcp="$cRes"
	elif procParmL "--ipv4" "$1" ; then 
    	ipv4="$cRes"
	elif procParmL "--gateway" "$1" ; then 
    	gateway="$cRes"
	elif procParmL "--dns1" "$1" ; then 
    	dns1="$cRes"
	elif procParmL "--dns2" "$1" ; then 
    	dns2="$cRes"
   	elif [ -z "$1" ] ; then 
    	break # Ключи кончились 
   	else 
    	echo "Error: Input key is not found: ${1}" 1>&2 
    	exit 1 
   fi
   shift
done

# addMsgFilter is required to assemble filtering mask which is applied to output data
addMsgFilter()
{

	if [ -z $msg_filter ] ; then
		msg_filter="$1"
	else
		msg_filter="${msg_filter}|$1"
	fi
}

if [ $command == "get" ] ; then
	msg_filter="ipv4.addresses:|ipv4.dns:|ipv4.gateway:|ipv4.method:"
	nmcli conn show $device | grep -E "${msg_filter}"
elif [ $command == "set" ] ; then
	if [ $ipv4 != 0 ] ; then
		nmcli conn modify "$device" ipv4.method $dhcp
		nmcli conn modify "$device" ipv4.address $ipv4
		addMsgFilter "ipv4.addresses:"
		addMsgFilter "ipv4.method:"
	else
		nmcli conn modify "$device" ipv4.method $dhcp
		addMsgFilter "ipv4.method:"
	fi
	if [ $dhcp != 0 ] ; then
		nmcli conn modify "$device" ipv4.method $dhcp
		addMsgFilter "ipv4.method:"
	fi
	if [ $gateway != 0 ] ; then
		nmcli conn modify "$device" ipv4.gateway $gateway
		addMsgFilter "ipv4.gateway:"
	fi
	if [ $dns1 != 0 ] ; then
		nmcli conn modify "$device" ipv4.dns $dns1
		addMsgFilter "ipv4.dns:"

	fi
	if [ $dns2 != 0 ] ; then
		nmcli conn modify "$device" ipv4.ignore-auto-dns yes
		nmcli conn modify "$device" +ipv4.dns $dns2
		nmcli conn modify "$device" ipv4.ignore-auto-dns no
		addMsgFilter "ipv4.dns:"
	fi

	nmcli connection down $device
	nmcli connection up $device
	
	#msg_filter="ipv4.addresses:|ipv4.dns:|ipv4.gateway:|ipv4.method:"
	#nmcli conn show $device | grep -E "${msg_filter}"

fi
