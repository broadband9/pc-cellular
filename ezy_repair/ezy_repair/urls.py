from django.contrib import admin
from django.urls import path, include
from django.conf import settings
from django.conf.urls.static import static

urlpatterns = [
    path('admin/', admin.site.urls),  # Admin under /ezy_repair/


    # App-specific URLs
    path('', include('customers.urls')),
    path('repairs/', include('repairs.urls')),
] + static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
