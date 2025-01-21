from django.contrib import admin
from .models import *

@admin.register(Repair)
class RepairAdmin(admin.ModelAdmin):
    list_display = [ 'customer', 'device_type', 'status', 'estimated_cost']
    search_fields = [ 'customer__name']
    list_filter = ['device_type', 'status']


admin.site.register([ActivityLog, Make, Location, RepairStatus, TechnicianNotes, Sites])
