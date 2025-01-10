from django.contrib import admin
from django.urls import path, include
from django.conf import settings
from django.conf.urls.static import static

urlpatterns = [
    path('ezy_repair/admin/', admin.site.urls),  # Admin under /ezy_repair/


    # App-specific URLs
    path('ezy_repair/', include('customers.urls')),
    path('ezy_repair/repairs/', include('repairs.urls')),
] + static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
