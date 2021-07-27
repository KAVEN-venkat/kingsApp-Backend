<html>
	<head>
		<style type="text/css">
			body{ background: #F3F5FA; }
			.container{
				width:70%;
				margin: 0 auto;
			}
			.brand-logo{
				text-align: center;
			}
			.brand-logo img{ margin: 0 auto 35px; }
			.content{ 
				background:#fff;
				border-radius:45px;
				box-shadow: 0 3px 10px 0 rgba(0,0,0,0.09);
				padding: 3rem !important;
			}
			.link_btn{
				color:#ffffff;
				border-radius: 25px;
			    background-color: #BFD45B;
			    font-family: "Nunito Sans";
			    font-size: 16px;
			    font-weight: bold;
			    text-align: center;
			    padding: 12px 78px;
			    margin: 30px auto 15px;
			    border: none;
			    cursor: pointer;
			    display: block;
			    text-decoration: none;
			    width:20%;
			}
		</style>
  	</head>
  <body>
  	<div class="container">
	  	<div class="brand-logo"> 
	  		<img src="https://maxartkiller.com/website/Fimobile/Fimobile-HTML/img/logo-header.png" alt="logo">
	  	</div>
	  	<div class="content">
		    <h2>HI {{ $first_name }} {{ $last_name }}</h2>
		    <p>Your account registration otp is <strong>{{$otp}}</strong>.</p>
		</div>
	</div>
  </body>
</html>