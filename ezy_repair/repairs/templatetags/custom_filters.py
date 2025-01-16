from django import template

register = template.Library()

@register.filter(name='replace_underscores')
def replace_underscores(value):
    """
    Custom filter to replace underscores with spaces and capitalize words.
    Usage: {{ value|replace_underscores }}
    """
    return value.replace('_', ' ').title()

@register.filter
def get_item(value, arg):
    return getattr(value, arg, None)