from django.urls import path
from . import views

urlpatterns = [
    path('', views.repairs_list, name='repairs_list'),
    path('add/', views.add_repair_status, name='add_repair_status'),
    path('add_location/', views.add_location, name='add_location'),
    path('add_make/', views.add_make, name='add_make'),
    path('edit_repair_status/<int:id>/', views.edit_repair_status, name='edit_repair_status'),
    path('delete_repair_status/<int:id>/', views.edit_repair_delete, name='delete_repair_status'),
    path('add_repair/', views.add_repair, name='add_repair'),
    path('edit/<int:pk>/', views.edit_repair, name='edit_repair'),
    path('delete/<int:pk>/', views.delete_repair, name='delete_repair'),
    path('locations/', views.locations, name='locations'),
    path('edit_locations/<int:pk>', views.edit_location, name='edit_location'),
    path('delete_locations/<int:pk>', views.location_delete, name='delete_location'),
    path('makes/', views.makes, name='makes'),
    path('makes_edit/<int:pk>/', views.edit_make, name='edit_make'),
    path('makes_delete/<int:pk>/', views.delete_make, name='delete_make'),
    path('repair_statuses/', views.repair_statuses, name='repair_statuses'),
    path('activity_logs/', views.activity_logs, name='activity_logs'),
    path('api/global-search/', views.global_search, name='global_search'),
    path('add-customer/', views.add_customer, name='add_customer'),
]
