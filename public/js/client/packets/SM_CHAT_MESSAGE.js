var msg = this.get_msg();
if(this.get_username()) msg = this.get_username() + ': ' + msg;
writeMessage(msg, this.get_color());
