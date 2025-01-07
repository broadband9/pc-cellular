from django.urls import path
from . import views

urlpatterns = [
    path('', views.customers_list, name='customers_list'),
]
