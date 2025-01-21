from django.core.mail import EmailMessage
from django.core.mail.backends.smtp import EmailBackend
from django.conf import settings

# from ezy_repair.ezy_repair.settings import EMAIL_HOST, EMAIL_PORT, EMAIL_HOST_USER, EMAIL_HOST_PASSWORD, EMAIL_USE_TLS

DEFAULT_FROM_EMAIL = 'noreply@9.technology'  # Default sender email

def send_email_with_smtp_go(subject, plain_message, html_message, recipient_email):
    email_backend = EmailBackend(
        host=settings.EMAIL_HOST,
        port=settings.EMAIL_PORT,
        username=settings.EMAIL_HOST_USER,  # No username, leave empty
        password=settings.EMAIL_HOST_PASSWORD,  # Use API key here
        use_tls=settings.EMAIL_USE_TLS,
    )

    email = EmailMessage(
        subject=subject,
        body=plain_message,
        from_email=settings.DEFAULT_FROM_EMAIL,
        to=[recipient_email],
        connection=email_backend,
    )
    email.content_subtype = "html"  # Send as HTML
    email.send()
