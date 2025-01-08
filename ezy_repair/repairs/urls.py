from django.urls import path
from . import views

urlpatterns = [
    path('', views.repairs_list, name='repairs_list'),
    path('add/', views.add_repair, name='add_repair'),
    path('edit/<int:pk>/', views.edit_repair, name='edit_repair'),
    path('delete/<int:pk>/', views.delete_repair, name='delete_repair'),
    path('locations/', views.locations, name='locations'),
    path('makes/', views.makes, name='makes'),
    path('repair_statuses/', views.repair_statuses, name='repair_statuses'),
    path('activity_logs/', views.activity_logs, name='activity_logs'),
]
