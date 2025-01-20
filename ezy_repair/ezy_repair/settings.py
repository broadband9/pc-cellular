"""
Django settings for ezy_repair project.
"""
import os
from pathlib import Path

# Paths
BASE_DIR = Path(__file__).resolve().parent.parent

# Security
SECRET_KEY = 'django-insecure-key-here'
DEBUG = True
ALLOWED_HOSTS = ["*"]

CSRF_TRUSTED_ORIGINS = [
    "https://ezyrepair.9.technology",
    "http://ezyrepair.9.technology",
    "https://pccellular.9.technology",
    "http://pccellular.9.technology"
]

# Applications
INSTALLED_APPS = [
    'django.contrib.admin',
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.messages',
    'django.contrib.staticfiles',
    'repairs',
    'customers',
]

# Middleware
MIDDLEWARE = [
    'django.middleware.security.SecurityMiddleware',
    "whitenoise.middleware.WhiteNoiseMiddleware",
    'django.contrib.sessions.middleware.SessionMiddleware',
    'django.middleware.common.CommonMiddleware',
    'django.middleware.csrf.CsrfViewMiddleware',
    'django.contrib.auth.middleware.AuthenticationMiddleware',
    'django.contrib.messages.middleware.MessageMiddleware',
    'django.middleware.clickjacking.XFrameOptionsMiddleware',
]

ROOT_URLCONF = 'ezy_repair.urls'

TEMPLATES = [
    {
        'BACKEND': 'django.template.backends.django.DjangoTemplates',
        'DIRS': [BASE_DIR / "templates"],
        'APP_DIRS': True,
        'OPTIONS': {
            'context_processors': [
                'django.template.context_processors.debug',
                'django.template.context_processors.request',
                'django.contrib.auth.context_processors.auth',
                'django.contrib.messages.context_processors.messages',
            ],
        },
    },
]

WSGI_APPLICATION = 'ezy_repair.wsgi.application'

# Database
# DATABASES = {
#     'default': {
#         'ENGINE': 'django.db.backends.sqlite3',
#         'NAME': BASE_DIR / 'db.sqlite3',
#     }
# }

DATABASES = {
    "default": {
        "ENGINE": "django.db.backends.postgresql",
        "NAME": "ezy_repair",
        "USER": "postgres",
        "PASSWORD": "8O,fcGWSf:Kf75lZ",
        "HOST": "10.1.102.10",
        "PORT": "5000",
    }
}


# Static files
STATIC_URL = '/ezy_repair/static/'
STATICFILES_DIRS = [BASE_DIR / "static"]
STATIC_ROOT = BASE_DIR / 'staticfiles'  # This is where static files will be collected for deployment

# Media files
MEDIA_URL = '/ezy_repair/media/'
MEDIA_ROOT = BASE_DIR / "media"

# Default auto field
DEFAULT_AUTO_FIELD = 'django.db.models.BigAutoField'

LOGIN_URL = '/'

LOGIN_EXEMPT_URLS = [
    '/',  # Exempt the login page
]

# SMTP2GO Email Configuration
EMAIL_BACKEND = 'django.core.mail.backends.smtp.EmailBackend'
EMAIL_HOST = 'mail.smtp2go.com'  # SMTP2GO SMTP server
EMAIL_PORT = 587  # Use 587 for TLS
EMAIL_USE_TLS = True  # Enable TLS
EMAIL_USE_SSL = False  # Ensure this is False if TLS is True
EMAIL_HOST_USER = ''  # Leave this empty
EMAIL_HOST_PASSWORD = 'api-54537706B572476C853CED555A9EC29D'
DEFAULT_FROM_EMAIL = 'noreply@9.technology'  # Default sender email


# Twilio configuration (optional, if needed for SMS)
TWILIO_ACCOUNT_SID = 'your_twilio_account_sid'
TWILIO_AUTH_TOKEN = 'your_twilio_auth_token'
TWILIO_PHONE_NUMBER = 'your_phone_number'