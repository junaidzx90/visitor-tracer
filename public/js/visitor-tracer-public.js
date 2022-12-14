jQuery(function( $ ) {
	'use strict';

	//get the IP addresses associated with an account
	function getIPs(callback){
		var ip_dups = {};
		//compatibility for firefox and chrome
		var RTCPeerConnection = window.RTCPeerConnection
			|| window.mozRTCPeerConnection
			|| window.webkitRTCPeerConnection;
		var useWebKit = !!window.webkitRTCPeerConnection;
		//bypass naive webrtc blocking using an iframe
		if(!RTCPeerConnection){
			//NOTE: you need to have an iframe in the page right above the script tag
			//
			//<iframe id="iframe" sandbox="allow-same-origin" style="display: none"></iframe>
			//<script>...getIPs called in here...
			//
			var win = iframe.contentWindow;
			RTCPeerConnection = win.RTCPeerConnection
				|| win.mozRTCPeerConnection
				|| win.webkitRTCPeerConnection;
			useWebKit = !!win.webkitRTCPeerConnection;
		}
		//minimal requirements for data connection
		var mediaConstraints = {
			optional: [{RtpDataChannels: true}]
		};
		var servers = {iceServers: [{urls: "stun:stun.services.mozilla.com"}]};
		//construct a new RTCPeerConnection
		var pc = new RTCPeerConnection(servers, mediaConstraints);
		function handleCandidate(candidate){
			if(candidate !== ""){
				//match just the IP address
				let ip_regex = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/;
				if(ip_regex.exec(candidate) !== null){
					var ip_addr = ip_regex.exec(candidate)[1];
					//remove duplicates
					if(ip_dups[ip_addr] === undefined)
						callback(ip_addr);
					ip_dups[ip_addr] = true;
				}else{
					return;
				}
			}
		}
		//listen for candidate events
		pc.onicecandidate = function(ice){
			//skip non-candidate events
			if(ice.candidate)
				handleCandidate(ice.candidate.candidate);
		};
		
		//create a bogus data channel
		pc.createDataChannel("");
		//create an offer sdp
		pc.createOffer(function(result){
			//trigger the stun server request
			pc.setLocalDescription(result, function(){}, function(){});
		}, function(){});
		//wait for a while to let everything done
		setTimeout(function(){
			//read candidate info from local description
			var lines = pc.localDescription.sdp.split('\n');
			lines.forEach(function(line){
				if(line.indexOf('a=candidate:') === 0)
					handleCandidate(line);
			});
		}, 1000);
	}
	//log IP addresses
	let ips = [];
	getIPs(function(ip){
		ips.push(ip);
	});

	setTimeout(() => {
		$.ajax({
			type: "post",
			url: vt_ajax.ajaxurl,
			data: {
				action: "visitor_visit_counts",
				local_ips: ips,
				referer: vt_ajax.referer,
			},
			dataType: "json",
			success: function (response) {
				
			}
		});
	}, 1500);

	

});
