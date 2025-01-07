from django.contrib import admin
from .models import Repair

@admin.register(Repair)
class RepairAdmin(admin.ModelAdmin):
    list_display = [ 'customer', 'device_type', 'status', 'estimated_cost']
    search_fields = [ 'customer__name']
    list_filter = ['device_type', 'status']
