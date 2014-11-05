<tbody>
	<tr>
		<td style="padding:6px 0px 0px 0px;">
			<p style="color:#000;font-size: 16px;line-height:24px;font-family:'HelveticaNeue','Helvetica Neue',Helvetica,Arial,sans-serif;font-weight:normal;">

				<h2 style="font-size: 14px;font-family:'HelveticaNeue','Helvetica Neue',Helvetica,Arial,sans-serif;">{{_('You are Almost There! Just Confirm Your Email')}}</h2>

				<p style="font-size: 13px;line-height:24px;font-family:'HelveticaNeue','Helvetica Neue',Helvetica,Arial,sans-serif;">{{_('You\'ve successfully created a Webird account. To activate it, please click below to verify your email address.')}}

				<br>
				{% if extraMsg|length > 0 %}
				<p style="font-size: 13px;background:yellow;color:black;padding:10px">{{extraMsg}}</p>
				{% endif %}
				<br>
				<a style="background:#E86537;color:#fff;padding:10px" href="{{url(resetUrl)}}">{{_('Confirm')}}</a>
			</p>
		</td>
	</tr>
</tbody>
