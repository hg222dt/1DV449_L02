function Message(message, date){

    this.getText = function() {
        return message;
    }

    this.setText = function(_text) {
        message = text;
    }

    this.getDate = function() {
        return date;
    }

    this.setDate = function(_date) {
        date = date;
    }
}

Message.prototype.toString = function(){
    return this.getText()+" ("+this.getDate()+")";
}

Message.prototype.getHTMLText = function() {
      
    return this.getText().replace(/[\n\r]/g, "<br />");
}

Message.prototype.getDateText = function() {
    return this.getDate().toLocaleTimeString();
}





var MessageBoard = {

    messages: [],
    textField: null,
    messageArea: null,

    init:function(e)
    {
	
		    MessageBoard.textField = document.getElementById("inputText");
		    MessageBoard.nameField = document.getElementById("inputName");
            MessageBoard.messageArea = document.getElementById("messagearea");
            MessageBoard.csrfToken = document.getElementById("csrfToken");
            MessageBoard.highestMessageId = null;

            // Add eventhandlers    
            document.getElementById("inputText").onfocus = function(e){ this.className = "focus"; }
            document.getElementById("inputText").onblur = function(e){ this.className = "blur" }
            document.getElementById("buttonSend").onclick = function(e) {MessageBoard.sendMessage(); return false;}
    
            MessageBoard.textField.onkeypress = function(e){ 
                                                    if(!e) var e = window.event;
                                                    
                                                    if(e.keyCode == 13 && !e.shiftKey){
                                                        MessageBoard.sendMessage(); 
                                                       
                                                        return false;
                                                    }
                                                }

            MessageBoard.getMessages();
    
    },
    setHighestMessageId:function(newId) {
        if(MessageBoard.highestMessageId == null) {
            MessageBoard.highestMessageId = newId;
            //console.log("MessageBoard.highestMessageId : " + MessageBoard.highestMessageId);
        } else if (newId > MessageBoard.highestMessageId) {
            MessageBoard.highestMessageId = newId;
        }
    },
    getMessages:function() {
        $.ajax({
			type: "GET",
			url: "functions.php",
			data: {function: "getMessages"}
		}).done(function(data) { // called when the AJAX call is ready
						
			data = JSON.parse(data);
		
			
			for(var mess in data) {
				var obj = data[mess];
			    var text = obj.name +" said:\n" +obj.message;
				var mess = new Message(text, new Date());
                var clientMessageID = MessageBoard.messages.push(mess)-1;
                var dbMessageId = obj.serial;

                MessageBoard.setHighestMessageId(parseInt(dbMessageId));
    
                MessageBoard.renderMessage(clientMessageID);
				
			}
			document.getElementById("nrOfMessages").innerHTML = MessageBoard.messages.length;
			

            MessageBoard.pollDatabase();
		});
	
        

    },
    pollDatabase:function() {

        console.log("polling");

        $.ajax({
            type: "GET",
            url: "functions.php",
            data: {'function': "pollDatabase", 'highestMessageId': MessageBoard.highestMessageId},

            async: true,
            cache: false,
            timeout:50000,

            success: function(data){ 

                if(data != false) {

                    data = JSON.parse(data);
                
                    for(var mess in data) {
                        var obj = data[mess];
                        var text = obj.name +" said:\n" +obj.message;
                        var mess = new Message(text, new Date());
                        var clientMessageID = MessageBoard.messages.push(mess)-1;
                        var dbMessageId = obj.serial;

                        MessageBoard.setHighestMessageId(dbMessageId);
            
                        MessageBoard.renderMessage(clientMessageID);
                        
                    }
                    
                    document.getElementById("nrOfMessages").innerHTML = MessageBoard.messages.length;                

                    console.log("data: " + data);
                }

                setTimeout(
                    function(){ MessageBoard.pollDatabase() },
                    1000
                );


            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                setTimeout(
                    MessageBoard.pollDatabase(),
                    5000);
            }
        });

    },
    sendMessage:function(){

        if(MessageBoard.textField.value == "") return;

/*
        // Make call to ajax
        $.ajax({
			type: "GET",
		  	url: "post2.php",
		  	data: {function: "add", name: MessageBoard.nameField.value, message:MessageBoard.textField.value, csrfToken: MessageBoard.csrfToken.value}
		}).done(function(data) {

            console.log(data);
		});

        */

        $.ajax({
            type: "GET",
            url: "post.php",
            data: {function: "add", name: MessageBoard.nameField.value, message:MessageBoard.textField.value, csrfToken: MessageBoard.csrfToken.value},
            async: true,
            cache: false,
            timeout:50000,

            success: function(data){ 
                console.log("message sent: " + data);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
               console.log("error");
            
            }
        });
    
    },
    renderMessages: function(){
        // Remove all messages
        MessageBoard.messageArea.innerHTML = "";
     
        // Renders all messages.
        for(var i=0; i < MessageBoard.messages.length; ++i){
            MessageBoard.renderMessage(i);
        }        
        
        document.getElementById("nrOfMessages").innerHTML = MessageBoard.messages.length;
    },
    renderMessage: function(messageID){
        // Message div
        var div = document.createElement("div");
        div.className = "message";
       
        // Clock button
        aTag = document.createElement("a");
        aTag.href="#";
        aTag.onclick = function(){
			MessageBoard.showTime(messageID);
			return false;
		}

        var imgClock = document.createElement("div");
        imgClock.className="clockImage";
        imgClock.alt="Show creation time";


        aTag.appendChild(imgClock);
        div.appendChild(aTag);
       
        // Message text
        var text = document.createElement("p");
        text.innerHTML = MessageBoard.messages[messageID].getHTMLText();        
        div.appendChild(text);
            
        // Time - Should fix on server!
        var spanDate = document.createElement("span");
        spanDate.appendChild(document.createTextNode(MessageBoard.messages[messageID].getDateText()))

        div.appendChild(spanDate);        
        
        var spanClear = document.createElement("span");
        spanClear.className = "clear";

        div.appendChild(spanClear);

        MessageBoard.messageArea.insertBefore(div, MessageBoard.messageArea.firstChild);

    },
    removeMessage: function(messageID){
		if(window.confirm("Vill du verkligen radera meddelandet?")){
        
			MessageBoard.messages.splice(messageID,1); // Removes the message from the array.
        
			MessageBoard.renderMessages();
        }
    },
    showTime: function(messageID){
         
         var time = MessageBoard.messages[messageID].getDate();
         
         var showTime = "Created "+time.toLocaleDateString()+" at "+time.toLocaleTimeString();

         alert(showTime);
    },
    logout: function() {
        window.location = "index.php";
    }
}

window.onload = MessageBoard.init;