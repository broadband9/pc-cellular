from django.urls import path
from . import views

urlpatterns = [
    path('', views.dashboard, name='dashboard'),
    path('repairs/', views.repairs_list, name='repairs_list'),
    path('repair-statuses/', views.repair_statuses, name='repair_statuses'),
    path('locations/', views.locations, name='locations'),
    path('makes/', views.makes, name='makes'),
    path('activity-logs/', views.activity_logs, name='activity_logs'),
]
